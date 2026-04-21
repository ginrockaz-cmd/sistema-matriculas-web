<?php
require 'fpdf/fpdf.php';
require 'config.php';

$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');
date_default_timezone_set('America/Lima');

/* =========================
   Función segura de texto
========================= */
function txt($text) {
    return utf8_decode((string)$text);
}

/* =========================
   CONSULTA GENERAL
========================= */
$query = "
    SELECT 
        a.apellido_nombre,
        p.mes_pago,
        p.monto,
        p.fecha_pago,
        p.estado,
        p.observaciones,
        p.metodo_pago,
        p.detalle,
        p.codigo_operacion
    FROM pagos p
    INNER JOIN alumnos a ON a.id = p.alumno_id
    ORDER BY 
        a.apellido_nombre ASC,
        p.anio_escolar ASC,
        FIELD(p.mes_pago,
            'Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')
";

$result = $mysqli->query($query);
if (!$result) {
    die('Error en la consulta');
}

/* =========================
   CLASE PDF
========================= */
class PDF extends FPDF {

    var $widths;

    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,txt('Reporte General de Pagos - I.E.P. Divina Misericordia'),0,1,'C');
        $this->Ln(3);

        $this->SetFont('Arial','B',9);
        $this->SetFillColor(60, 90, 180);
        $this->SetTextColor(255);

        $this->widths = [55,18,18,22,18,30,22,28,22];

        $headers = [
            'Estudiante','Mes','Monto','Fecha','Estado',
            'Observación','Método','Detalle','Código'
        ];

        foreach ($headers as $i => $h) {
            $this->Cell($this->widths[$i],8,txt($h),1,0,'C',true);
        }
        $this->Ln();

        $this->SetFont('Arial','',9);
        $this->SetTextColor(0);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,5,txt('Sistema de Matrículas y Pensiones - I.E.P. Divina Misericordia'),0,0,'L');
        $this->Cell(0,5,date('d/m/Y h:i A'),0,0,'R');
    }

    /* ===== Calcula altura según texto ===== */
    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r",'',(string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb-1] == "\n") $nb--;
        $sep = -1;
        $i = 0; $j = 0; $l = 0; $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++; $sep = -1; $j = $i; $l = 0; $nl++;
                continue;
            }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else $i++;
        }
        return $nl;
    }

    /* ===== Fila ajustable ===== */
    function Row($data) {
        $nb = 0;
        foreach ($data as $i => $txt) {
            $nb = max($nb, $this->NbLines($this->widths[$i], txt($txt)));
        }
        $h = 6 * $nb;

        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage();

        foreach ($data as $i => $txt) {
            $w = $this->widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();

            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w,6,txt($txt),0,'C');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }
}

/* =========================
   CREAR PDF
========================= */
$pdf = new PDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$total_pagado   = 0;
$total_pendiente = 0;
$total_vencido  = 0;

/* =========================
   LLENAR TABLA
========================= */
while ($r = $result->fetch_assoc()) {

    $estado = strtolower($r['estado']);
    if ($estado === 'pagado')      $total_pagado += $r['monto'];
    elseif ($estado === 'pendiente') $total_pendiente += $r['monto'];
    elseif ($estado === 'vencido')   $total_vencido += $r['monto'];

    $pdf->Row([
        $r['apellido_nombre'],
        $r['mes_pago'],
        number_format($r['monto'],2),
        $r['fecha_pago'],
        ucfirst($r['estado']),
        $r['observaciones'] ?: '-',
        $r['metodo_pago'] ?: '-',
        $r['detalle'] ?: '-',
        $r['codigo_operacion'] ?: '-'
    ]);
}

/* =========================
   TOTALES
========================= */
$pdf->Ln(6);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,7,txt('TOTAL PAGADO: S/ '.number_format($total_pagado,2)),0,1,'R');
$pdf->Cell(0,7,txt('TOTAL PENDIENTE: S/ '.number_format($total_pendiente,2)),0,1,'R');

/* =========================
   SALIDA
========================= */
$pdf->Output('I','reporte_general_pagos.pdf');
?>
