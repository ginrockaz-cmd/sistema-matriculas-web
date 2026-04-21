<?php
require 'fpdf/fpdf.php';
require 'config.php';

$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

// ✅ Conversión segura UTF-8 → ISO-8859-1
function fixEncoding($text) {
    if ($text === null) return '';
    if (!mb_detect_encoding($text, 'UTF-8', true)) return $text;
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
}

// --- Consulta de matrículas ---
$query = "SELECT 
            m.id,
            a.apellido_nombre AS estudiante,
            g.nombre AS grado,
            m.anio_year,
            m.fecha_matricula,
            m.estado
          FROM matriculas m
          JOIN alumnos a ON m.alumno_id = a.id
          LEFT JOIN grados g ON m.grado_id = g.id
          ORDER BY m.id ASC";

$res = $mysqli->query($query);
if (!$res) {
    die('❌ Error en la consulta SQL: ' . $mysqli->error);
}

// --- Clase PDF personalizada ---
class PDF extends FPDF {

    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,fixEncoding('Reporte de Matrículas - I.E.P. Divina Misericordia'),0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',9);

        // 🔹 Texto principal (izquierda)
        $texto = fixEncoding('I.E.P. Divina Misericordia - Sistema de Matrículas y Pensiones');
        $this->Cell(0,5,$texto,0,0,'L');

        // 🔹 Fecha y hora (derecha)
        date_default_timezone_set('America/Lima');
        $fechaHora = date('d/m/Y H:i:s');
        $this->Cell(0,5,fixEncoding('Descargado el: ' . $fechaHora),0,0,'R');
    }

    // 🔹 Ajuste de texto largo
    function FitCell($w, $h, $text, $border=1, $ln=0, $align='C') {
        $text = fixEncoding($text);
        $strWidth = $this->GetStringWidth($text);
        if ($strWidth == 0) {
            $this->Cell($w, $h, '', $border, $ln, $align);
            return;
        }
        $ratio = ($w - 2) / $strWidth;
        $fontSize = $this->FontSizePt * min(1, $ratio);
        if ($fontSize < 7) $fontSize = 7;

        $this->SetFont($this->FontFamily, '', $fontSize);
        $this->Cell($w, $h, $text, $border, $ln, $align);
        $this->SetFont($this->FontFamily, '', 9);
    }
}

// --- Crear PDF ---
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();

// --- Configuración de tabla ---
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);

$w_id = 10;
$w_est = 65;
$w_grado = 40;
$w_anio = 30;
$w_fecha = 35;
$w_estado = 25;

$totalWidth = $w_id + $w_est + $w_grado + $w_anio + $w_fecha + $w_estado;
$marginX = ($pdf->GetPageWidth() - $totalWidth) / 2;
$pdf->SetX($marginX);

// --- Encabezado ---
$pdf->Cell($w_id,10,'ID',1,0,'C',true);
$pdf->Cell($w_est,10,fixEncoding('Estudiante'),1,0,'C',true);
$pdf->Cell($w_grado,10,fixEncoding('Grupo'),1,0,'C',true);
$pdf->Cell($w_anio,10,fixEncoding('Año Escolar'),1,0,'C',true);
$pdf->Cell($w_fecha,10,fixEncoding('Fecha Matrícula'),1,0,'C',true);
$pdf->Cell($w_estado,10,fixEncoding('Estado'),1,1,'C',true);

// --- Contenido ---
$pdf->SetFont('Arial','',9);
while ($r = $res->fetch_assoc()) {
    $pdf->SetX($marginX);
    $pdf->Cell($w_id,6,$r['id'],1,0,'C');
    $pdf->FitCell($w_est,6,$r['estudiante'],1,0,'C');
    $pdf->FitCell($w_grado,6,$r['grado'] ?: '—',1,0,'C');
    $pdf->Cell($w_anio,6,$r['anio_year'],1,0,'C');
    $pdf->Cell($w_fecha,6,$r['fecha_matricula'],1,0,'C');
    $pdf->Cell($w_estado,6,ucfirst(fixEncoding($r['estado'])),1,1,'C');
}

// --- Salida ---
$pdf->Output('I', 'Lista_Matriculas.pdf');
?>
