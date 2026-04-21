<?php
require 'config.php';
require 'fpdf/fpdf.php';

$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

$alumno_id = intval($_GET['alumno_id'] ?? 0);
if ($alumno_id <= 0) die("ID inválido");

// ---------------- Datos del alumno ----------------
$alu = $mysqli->query("SELECT * FROM alumnos WHERE id=$alumno_id")->fetch_assoc();
if (!$alu) die("Alumno no encontrado");

// Obtener estado de matrícula
$matricula = $mysqli->query("SELECT estado FROM matriculas WHERE alumno_id=$alumno_id LIMIT 1")->fetch_assoc();
$estado_alumno = $matricula['estado'] ?? 'activo';

// Grado del alumno
$resGrado = $mysqli->query("SELECT g.nombre AS grado FROM alumnos a LEFT JOIN grados g ON a.grado_id=g.id WHERE a.id=$alumno_id LIMIT 1");
$grado_nombre = ($resGrado && $resGrado->num_rows>0) ? $resGrado->fetch_assoc()['grado'] : '';

// ---------------- Funciones auxiliares ----------------
function obtenerMontosPorGrado($gradoNombre, $alumno_id, $anio, $mysqli) {
    $montos = ['matricula'=>null,'pension'=>null];

    $stmt = $mysqli->prepare("SELECT tipo, monto FROM modificar_montos_defecto WHERE alumno_id=? AND anio=?");
    if($stmt){
        $stmt->bind_param("ii", $alumno_id, $anio);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){
            $montos[$row['tipo']] = floatval($row['monto']);
        }
        $stmt->close();
    }

    if ($montos['matricula']===null || $montos['pension']===null) {
        $stmt = $mysqli->prepare("SELECT tipo, monto FROM modificar_montos_defecto WHERE grado=? AND anio=?");
        if($stmt){
            $stmt->bind_param("si", $gradoNombre, $anio);
            $stmt->execute();
            $res = $stmt->get_result();
            while($row = $res->fetch_assoc()){
                $montos[$row['tipo']] = floatval($row['monto']);
            }
            $stmt->close();
        }
    }

    if ($montos['matricula']===null) $montos['matricula'] = in_array($gradoNombre, ["ET","I3","I4","I5"]) ? 230 : (in_array($gradoNombre, ["P1","P2","P3A","P3B","P4A","P4B","P5A","P5B","P6"]) ? 310 : 320);
    if ($montos['pension']===null) $montos['pension'] = in_array($gradoNombre, ["ET","I3","I4","I5"]) ? 330 : (in_array($gradoNombre, ["P1","P2","P3A","P3B","P4A","P4B","P5A","P5B","P6"]) ? 385 : 395);

    return $montos;
}

function mesEspanol($mesNum){
    $meses = [
        1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
        7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
    ];
    return $meses[$mesNum] ?? 'Mes inválido';
}

// Determinar si el cargo está vencido
function cargoEstaVencido($anio, $mes, $fecha_pago = null){
    $hoy = new DateTime();
    if ($fecha_pago) return false; // ya pagado
    $fechaCargo = DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $anio, $mes));
    if(!$fechaCargo) return false;
    return $fechaCargo < $hoy;
}

// ---------------- Cargar pagos ----------------
$stmt = $mysqli->prepare("SELECT anio_escolar, mes_pago, monto, fecha_pago, estado, metodo_pago, detalle, codigo_operacion, observaciones FROM pagos WHERE alumno_id=? ORDER BY anio_escolar ASC, id ASC");
$stmt->bind_param("i",$alumno_id);
$stmt->execute();
$pagos_result = $stmt->get_result();
$pagos = $pagos_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mapMes = ["enero"=>1,"febrero"=>2,"marzo"=>3,"abril"=>4,"mayo"=>5,"junio"=>6,"julio"=>7,"agosto"=>8,"septiembre"=>9,"octubre"=>10,"noviembre"=>11,"diciembre"=>12];

// Agrupar pagos por año y mes
$pagos_por_anio = [];
foreach($pagos as $p){
    $mes_num = $mapMes[strtolower($p['mes_pago'])] ?? 0;
    $anio = intval($p['anio_escolar']);
    if($anio && $mes_num) $pagos_por_anio[$anio][$mes_num][] = $p;
}

// Rango de años
if(!empty($pagos_por_anio)){
    $añosRegistrados = array_keys($pagos_por_anio);
    $anio_inicio = min($añosRegistrados);
    $anio_final = max($añosRegistrados);
} else {
    $anio_inicio = intval(date('Y'));
    $anio_final = $anio_inicio;
}
$años = range($anio_inicio, $anio_final);

// Construir estado de cuenta respetando Pendiente/Vencido según cargoEstaVencido
$estadoCuenta = [];
foreach($años as $anio){
    $detalle = [];
    $total_pendiente = 0;
    $total_vencido = 0;
    $total_pagado = 0;

    for($mes=1;$mes<=12;$mes++){
        $concepto_tipo = ($mes<=2) ? 'Matrícula' : 'Pensión';
        $concepto_visual = mesEspanol($mes);
        $montos = obtenerMontosPorGrado($grado_nombre, $alumno_id, $anio, $mysqli);
        $monto_defecto = ($concepto_tipo=='Matrícula') ? $montos['matricula'] : $montos['pension'];

        if(isset($pagos_por_anio[$anio][$mes])){
            foreach($pagos_por_anio[$anio][$mes] as $p){
                $estado = ucfirst(strtolower($p['estado'] ?? 'Pendiente'));
                $pago_real = floatval($p['monto'] ?? 0);
                $fecha_pago = $p['fecha_pago'] ?: '-';
                $metodo_pago = $p['metodo_pago'] ?: '-';
                $detalle_pago = $p['detalle'] ?: '-';
                $codigo_operacion = $p['codigo_operacion'] ?: '-';
                $observaciones = $p['observaciones'] ?: '-';

                if($estado=='Pagado') $total_pagado += $pago_real;
                elseif($estado=='Pendiente') $total_pendiente += $monto_defecto;
                elseif($estado=='Vencido') $total_vencido += $monto_defecto;

                $detalle[] = [
                    'concepto'=>$concepto_visual,
                    'monto'=>$monto_defecto,
                    'pago_real'=>$pago_real,
                    'fecha_pago'=>$fecha_pago,
                    'estado'=>$estado,
                    'metodo_pago'=>$metodo_pago,
                    'detalle_pago'=>$detalle_pago,
                    'codigo_operacion'=>$codigo_operacion,
                    'observaciones'=>$observaciones
                ];
            }
        } else {
            // Si no hay pago registrado, aplicar cargoEstaVencido
            if($estado_alumno == 'retirado'){
    $estado = 'Retirado';
} elseif($estado_alumno == 'fallecido'){
    $estado = 'Fallecido';
} else {
    // alumno activo: evaluar vencido o pendiente
    $estado = cargoEstaVencido($anio,$mes) ? 'Vencido' : 'Pendiente';
}
            $detalle[] = [
                'concepto'=>$concepto_visual,
                'monto'=>$monto_defecto,
                'pago_real'=>0,
                'fecha_pago'=>'-',
                'estado'=>$estado,
                'metodo_pago'=>'-',
                'detalle_pago'=>'-',
                'codigo_operacion'=>'-',
                'observaciones'=>'-'
            ];
            if($estado=='Vencido') $total_vencido += $monto_defecto;
            else $total_pendiente += $monto_defecto;
        }
    }

    $estadoCuenta[$anio] = [
        'detalle'=>$detalle,
        'total_pendiente'=>$total_pendiente,
        'total_vencido'=>$total_vencido,
        'total_pagado'=>$total_pagado
    ];
}

// ---------------- PDF ----------------
class PDF extends FPDF {
    function Header(){
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,utf8_decode('Estado de Cuenta - I.E.P. Divina Misericordia'),0,1,'C');
        $this->Ln(5);
    }
    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(0);
        // izquierda
$this->Cell(0,10,utf8_decode('Sistema de Matrículas y Pensiones - I.E.P. Divina Misericordia'),0,0,'L');
       // Establecer zona horaria de Lima, Perú
date_default_timezone_set('America/Lima');

// derecha
$this->Cell(0,10,date('d/m/Y H:i'),0,0,'R');  // H:i mostrará la hora exacta del servidor con la zona correcta
    }
}

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// Datos del alumno
$pdf->Cell(0,8,utf8_decode("Alumno: ").utf8_decode($alu['apellido_nombre']),0,1);
$pdf->Cell(0,8,utf8_decode("Apoderado: ").utf8_decode($alu['apoderado']),0,1);
$pdf->Cell(0,8,"DNI: ".$alu['dni'],0,1);
$pdf->Ln(4);

$headers = ['Concepto','Monto (S/)','Pago (S/)','Fecha Pago','Estado','Método','Detalle','Código','Observaciones'];
$w = [40,25,25,30,25,35,35,35,50];

// Recorrer años
foreach($estadoCuenta as $anio => $data){
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,8,utf8_decode("Año Escolar: $anio"),0,1);
    
    // Cabecera
    foreach($headers as $i=>$h){
        $pdf->SetFillColor(63,81,181);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($w[$i],8,utf8_decode($h),1,0,'C',true);
    }
    $pdf->Ln();
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial','',9);

    foreach($data['detalle'] as $row){
        $estado = $row['estado'];
        if($estado=='Pagado') $pdf->SetFillColor(200,245,200);
        elseif($estado=='Pendiente') $pdf->SetFillColor(255,243,205);
        elseif($estado=='Vencido') $pdf->SetFillColor(255,205,210);
        elseif($estado=='Retirado') $pdf->SetFillColor(204,229,255);  // azul claro
elseif($estado=='Fallecido') $pdf->SetFillColor(255,204,204); // rosa claro
        else $pdf->SetFillColor(255,255,255);

        $pdf->Cell($w[0],8,utf8_decode($row['concepto']),1);
        $pdf->Cell($w[1],8,number_format($row['monto'],2),1);
        $pdf->Cell($w[2],8,number_format($row['pago_real'],2),1);
        $pdf->Cell($w[3],8,utf8_decode($row['fecha_pago']),1);
        $pdf->Cell($w[4],8,utf8_decode($estado),1,0,'C',true);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell($w[5],8,utf8_decode($row['metodo_pago']),1);
        $pdf->Cell($w[6],8,utf8_decode($row['detalle_pago']),1);
        $pdf->Cell($w[7],8,utf8_decode($row['codigo_operacion']),1);
        $pdf->Cell($w[8],8,utf8_decode($row['observaciones']),1);
        $pdf->Ln();
    }

   $pdf->Ln(2);
$pdf->SetFont('Arial','B',11);

// Total Pendiente en una línea
$pdf->Cell(0,8,"Total Pendiente: S/ ".number_format($data['total_pendiente'],2),0,1,'R');

// Total Vencido en la siguiente línea
$pdf->Cell(0,8,"Total Vencido: S/ ".number_format($data['total_vencido'],2),0,1,'R');

$pdf->Ln(4);

}

$pdf->Output('I','estado_cuenta_'.$alu['apellido_nombre'].'.pdf');
