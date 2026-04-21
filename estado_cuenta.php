<?php  
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

/* -------------------------------
   FUNCIONES AUXILIARES
--------------------------------*/
function obtenerMontosPorGrado($gradoNombre, $alumno_id, $anio, $mysqli) {
    $montos = ['matricula'=>null,'pension'=>null];

    // 1️⃣ Monto personalizado por alumno
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

    // 2️⃣ Monto por grado
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

    // 3️⃣ Valores por defecto
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
    if ($fecha_pago) {
        // Si el pago ya fue registrado, no está vencido
        return false;
    }
    $fechaCargo = DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $anio, $mes));
    if(!$fechaCargo) return false;
    return $fechaCargo < $hoy;
}

/* -------------------------------
   CARGAR DATOS Y GENERAR ESTADO
--------------------------------*/
$alumno_id = intval($_GET['alumno_id'] ?? 0);
$alu = null;
$estadoCuenta = [];

if ($alumno_id > 0) {
    // Alumno
    $r = $mysqli->query("SELECT * FROM alumnos WHERE id = $alumno_id LIMIT 1");
    if ($r && $r->num_rows > 0) $alu = $r->fetch_assoc();

    // Obtener estado de matrícula
$matricula = $mysqli->query("SELECT estado FROM matriculas WHERE alumno_id=$alumno_id LIMIT 1")->fetch_assoc();
if($matricula) {
    $estado_alumno = strtolower($matricula['estado']);
} else {
    $estado_alumno = strtolower($alu['estado'] ?? 'activo'); // usar estado desde alumnos
}

    // Grado del alumno
    $resGrado = $mysqli->query("SELECT g.nombre AS grado FROM alumnos a LEFT JOIN grados g ON a.grado_id=g.id WHERE a.id=$alumno_id LIMIT 1");
    $grado_nombre = ($resGrado && $resGrado->num_rows>0) ? $resGrado->fetch_assoc()['grado'] : '';

    // Todos los pagos existentes
    $stmt = $mysqli->prepare("SELECT anio_escolar, mes_pago, monto, fecha_pago, estado, metodo_pago, detalle, codigo_operacion, observaciones FROM pagos WHERE alumno_id=? ORDER BY anio_escolar ASC, id ASC");
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $pagos_result = $stmt->get_result();
    $pagos = $pagos_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $mapMes = ["enero"=>1,"febrero"=>2,"marzo"=>3,"abril"=>4,"mayo"=>5,"junio"=>6,"julio"=>7,"agosto"=>8,"septiembre"=>9,"octubre"=>10,"noviembre"=>11,"diciembre"=>12];

    // Agrupar pagos por año y mes → PERMITIR MÚLTIPLES PAGOS POR MES
    $pagos_por_anio = [];
    foreach($pagos as $p){
        $mes_num = $mapMes[strtolower($p['mes_pago'])] ?? 0;
        $anio = intval($p['anio_escolar']);
        if ($anio && $mes_num){
            $pagos_por_anio[$anio][$mes_num][] = $p; // [] para permitir múltiples registros
        }
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

    // Construir estado de cuenta
    foreach($años as $anio){
        $detalle = [];
        $total_pendiente = 0;
        $total_vencido = 0;
        $total_pagado = 0;

        for($mes = 1; $mes <= 12; $mes++){
            $concepto = ($mes <= 2) ? 'Matrícula' : 'Pensión';
            $concepto_visual = mesEspanol($mes);
            $montos = obtenerMontosPorGrado($grado_nombre, $alumno_id, $anio, $mysqli);
            $monto_defecto = ($concepto == 'Matrícula') ? $montos['matricula'] : $montos['pension'];

            if (isset($pagos_por_anio[$anio][$mes])){
                foreach($pagos_por_anio[$anio][$mes] as $p){
                    $estado = ucfirst(strtolower($p['estado']));
                    $pago_real = isset($p['monto']) ? floatval($p['monto']) : 0;
                    $fecha_pago = $p['fecha_pago'] ?: '-';
                    $metodo_pago = $p['metodo_pago'] ?: '-';
                    $detalle_pago = $p['detalle'] ?: '-';
                    $codigo_operacion = $p['codigo_operacion'] ?: '-';
                    $observaciones = $p['observaciones'] ?: '-';

                    if ($estado == 'Pagado') $total_pagado += $pago_real;
                    elseif ($estado == 'Pendiente') $total_pendiente += $monto_defecto;
                    elseif ($estado == 'Vencido') $total_vencido += $monto_defecto;

                    $detalle[] = [
                        'concepto' => $concepto_visual,
                        'monto' => $monto_defecto,
                        'pago_real' => $pago_real,
                        'fecha_pago' => $fecha_pago,
                        'estado' => $estado,
                        'metodo_pago' => $metodo_pago,
                        'detalle_pago' => $detalle_pago,
                        'codigo_operacion' => $codigo_operacion,
                        'observaciones' => $observaciones
                    ];
                }
            } else {
                // Determinar estado del concepto
if($estado_alumno == 'retirado'){
    $estado = 'Retirado';
} elseif($estado_alumno == 'fallecido'){
    $estado = 'Fallecido';
} else {
    $estado = cargoEstaVencido($anio,$mes) ? 'Vencido' : 'Pendiente';
}

// Agregar al detalle siempre
$detalle[] = [
    'concepto' => $concepto_visual,
    'monto' => $monto_defecto,
    'pago_real' => 0,
    'fecha_pago' => '-',
    'estado' => $estado,
    'metodo_pago' => '-',
    'detalle_pago' => '-',
    'codigo_operacion' => '-',
    'observaciones' => '-'
];

// Solo sumar totales si alumno activo
if ($estado_alumno == 'activo') {
    if ($estado == 'Vencido') $total_vencido += $monto_defecto;
    if ($estado == 'Pendiente') $total_pendiente += $monto_defecto;
}
            }
        }

        $estadoCuenta[$anio] = [
            'detalle' => $detalle,
            'total_pendiente' => $total_pendiente,
            'total_vencido' => $total_vencido,
            'total_pagado' => $total_pagado
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - Colegio Divina Misericordia</title>
    <style>
        body { font-family: "Segoe UI", Arial; background: #f2f6fc; margin: 40px; }
        h2 { text-align: center; color: #2c3e50; }
        h3 { text-align: center; color: #1a5276; }
        form { text-align: center; margin-bottom: 25px; position: relative; }
        input[type="text"] { padding: 10px; width: 300px; border-radius: 8px; border: 1px solid #ccc; }
        button { padding: 10px 15px; background: #1976d2; color: white; border-radius: 8px; border: none; cursor: pointer; }
        button:hover { background: #125ca1; }
        table { border-collapse: collapse; width: 100%; background: white; border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.15); }
        th { background: #1976d2; color: white; padding: 10px; text-align: center; }
        td { padding: 10px; text-align: center; border-bottom: 1px solid #eee; }
        .estado-pagado { color: #279c2dff; font-weight: bold; }
        .estado-pendiente { color: #e67e22; font-weight: bold; }
        .estado-vencido { color: #8f032dff; font-weight: bold; }
        .estado-retirado { 
    background-color: #cce5ff;  /* azul claro de fondo */
    color: #b805ffff;             /* texto azul oscuro */
    font-weight: bold; 
}
.estado-fallecido { 
    background-color: #ffd6d6;  /* rosa suave de fondo */
    color: #05d9f5ff;             /* texto rojo oscuro */
    font-weight: bold; 
}

/* ===============================
   BOTONES PRINCIPALES
================================= */
a.volver, .pdf-btn {
    display: inline-flex;                 /* centrado vertical y horizontal */
    align-items: center;
    justify-content: center;
    padding: 10px 20px;                   /* espacio para el texto */
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    color: #ffffff;                        /* texto siempre visible */
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    margin-right: 10px;                     /* separación entre botones */
    margin-bottom: 20px;
    min-width: 180px;                       /* ancho mínimo para texto */
    text-align: center;
}

/* Botón Volver al Inicio */
a.volver {
    background-color: #1976d2;            /* azul institucional */
}
a.volver:hover {
    background-color: #125ca1;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0,0,0,0.25);
}

        .pdf-btn { background: #4caf50; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; }
        #searchResults { 
            position: relative;
            width: 300px;
            margin: 5px auto 0;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            display: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }
        #searchResults a { display:block; padding:8px; border-bottom:1px solid #eee; text-decoration:none; color:#333; }
        #searchResults a:hover { background:#f3f3f3; }
    </style>
</head>
<body>

<h2>📘 Estado de Cuenta - Colegio Divina Misericordia</h2>
<a href="estado_cuenta_pdf.php?alumno_id=<?= $alumno_id ?>" class="pdf-btn" target="_blank"> 📄 Descargar PDF
<a class="volver" href="dashboard.php">← Volver al Inicio</a>

<form id="formBusqueda" method="get">
    <input type="hidden" name="alumno_id" id="alumno_id">
    <input type="text" id="studentSearch" placeholder="Escriba el apellido del estudiante..." autocomplete="off">
    <div id="searchResults"></div>
    <button type="submit">Ver Estado de Cuenta</button>
</form>

<?php if($alu): ?>
<h3>Alumno: <b><?= htmlspecialchars($alu['apellido_nombre']) ?></b> — Grado: <b><?= htmlspecialchars($grado_nombre) ?></b></h3>

<?php foreach($estadoCuenta as $anio => $data): ?>
<h3>Año Escolar: <?= $anio ?></h3>
<table border="1" width="100%">
<tr>
<th>Concepto</th><th>Monto</th><th>Pago</th><th>Fecha Pago</th><th>Estado</th>
<th>Método</th><th>Detalle</th><th>Código</th><th>Obs.</th>
</tr>

<?php foreach($data['detalle'] as $row): 
   $cssEstado = ($row['estado']=='Pagado') ? 'estado-pagado' :
             (($row['estado']=='Pendiente') ? 'estado-pendiente' :
             (($row['estado']=='Vencido') ? 'estado-vencido' :
             (($row['estado']=='Retirado') ? 'estado-retirado' :
             (($row['estado']=='Fallecido') ? 'estado-fallecido' : 'estado-pendiente'))));

?>
<tr>
<td><?= htmlspecialchars($row['concepto']) ?></td>
<td><?= number_format($row['monto'],2) ?></td>
<td><?= number_format($row['pago_real'],2) ?></td>
<td><?= $row['fecha_pago'] ?></td>
<td class="<?= $cssEstado ?>"><?= $row['estado'] ?></td>
<td><?= htmlspecialchars($row['metodo_pago']) ?></td>
<td><?= htmlspecialchars($row['detalle_pago']) ?></td>
<td><?= htmlspecialchars($row['codigo_operacion']) ?></td>
<td><?= htmlspecialchars($row['observaciones']) ?></td>
</tr>
<?php endforeach; ?>

<tr>
    <td colspan="2"><b>Totales</b></td>
    <td><b><?= number_format($data['total_pagado'],2) ?></b></td>
    <td colspan="1"></td>
    <td>
        <b>Pendiente: <?= number_format($data['total_pendiente'],2) ?><br>
        Vencido: <?= number_format($data['total_vencido'],2) ?></b>
    </td>
    <td colspan="4"></td>
</tr>
</table>
<?php endforeach; ?>

<?php elseif ($alumno_id): ?>
    <p style="text-align:center;">Alumno no encontrado o sin matrícula.</p>
<?php endif; ?>

<!-- Script de búsqueda: mantiene tu JS original -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('studentSearch');
    const results = document.getElementById('searchResults');
    const alumnoIdInput = document.getElementById('alumno_id');

    input.addEventListener('keyup', () => {
        const query = input.value.trim();
        if (query.length < 2) { results.style.display = 'none'; return; }

        fetch('students_search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                results.innerHTML = '';
                if (data.length === 0) { results.style.display = 'none'; return; }

                data.forEach(student => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.textContent = student.apellido_nombre;
                    item.addEventListener('click', e => {
                        e.preventDefault();
                        alumnoIdInput.value = student.id;
                        input.value = student.apellido_nombre;
                        results.innerHTML = '';
                        results.style.display = 'none';
                    });
                    results.appendChild(item);
                });

                results.style.display = 'block';
            })
            .catch(err => console.error('Error al buscar estudiante:', err));
    });

    document.addEventListener('click', e => {
        if (!results.contains(e.target) && e.target !== input) results.style.display = 'none';
    });
});
</script>
</body>
</html>
