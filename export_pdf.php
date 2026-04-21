<?php 
require 'fpdf/fpdf.php';
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8');

// ✅ Función segura para convertir texto a ISO si está en UTF-8
function fixEncoding($text) {
    if (!mb_detect_encoding($text, 'UTF-8', true)) {
        return utf8_decode($text);
    }
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
}

$grado_id = intval($_GET['grado_id'] ?? 0);
if (!$grado_id) die("Grupo no especificado.");

$grado = $mysqli->query("SELECT nombre FROM grados WHERE id=$grado_id")->fetch_assoc()['nombre'] ?? 'Sin nombre';

$res = $mysqli->query("SELECT id, apellido_nombre, dni, fecha_nacimiento, apoderado, telefono, correo 
                       FROM alumnos WHERE grado_id=$grado_id ORDER BY apellido_nombre ASC");

// --- CLASE PDF PERSONALIZADA ---
class PDF extends FPDF {
    // 🔹 Función que ajusta el texto si es muy largo
    function FitCell($w, $h, $text, $border=1, $ln=0, $align='L') {
        $text = fixEncoding($text);
        $strWidth = $this->GetStringWidth($text);
        if ($strWidth == 0) {
            $this->Cell($w, $h, '', $border, $ln, $align);
            return;
        }

        $ratio = ($w - 9) / $strWidth;
        $fontSize = $this->FontSizePt * $ratio;

        if ($fontSize < 5) $fontSize = 5; // tamaño mínimo

        $this->SetFont($this->FontFamily, '', $fontSize);
        $this->Cell($w, $h, utf8_decode($text), $border, $ln, $align);
        $this->SetFont($this->FontFamily, '', 6); // restaurar tamaño normal
    }

    // 🔹 Pie de página con fecha y hora
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',6);
        $this->SetTextColor(0,0,0);

        date_default_timezone_set('America/Lima');
        $fechaHora = date('d/m/Y H:i:s');

        $textoIzq = fixEncoding("I.E.P. Divina Misericordia - Sistema de Matrículas y Pensiones");
        $textoDer = fixEncoding("Descargado el: $fechaHora");

        // Izquierda
        $this->Cell(0,5,$textoIzq,0,0,'L');
        // Derecha
        $this->Cell(0,5,$textoDer,0,0,'R');
    }
}

// --- CREAR PDF ---
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();

// --- ENCABEZADO PRINCIPAL ---
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,fixEncoding("Lista de Estudiantes - $grado"),0,1,'C');
$pdf->Ln(4);

// --- CONFIGURACIÓN DE TABLA ---
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);

$w_id = 10;
$w_nombre = 65;
$w_dni = 22;
$w_fnac = 23;
$w_apoderado = 65;
$w_tel = 30;
$w_correo = 52;

$pdf->Cell($w_id,10,'ID',1,0,'C',true);
$pdf->Cell($w_nombre,10,'Estudiante',1,0,'C',true);
$pdf->Cell($w_dni,10,'DNI',1,0,'C',true);
$pdf->Cell($w_fnac,10,'F. Nac.',1,0,'C',true);
$pdf->Cell($w_apoderado,10,'Apoderado',1,0,'C',true);
$pdf->Cell($w_tel,10,fixEncoding('Teléfono'),1,0,'C',true);
$pdf->Cell($w_correo,10,'Correo',1,1,'C',true);

// --- CONTENIDO ---
$pdf->SetFont('Arial','',9);
while ($row = $res->fetch_assoc()) {
    $pdf->FitCell($w_id,6,$row['id'],1,0,'C');
    $pdf->Cell($w_nombre,6,fixEncoding($row['apellido_nombre']),1,0,'L');
    $pdf->Cell($w_dni,6,$row['dni'],1,0,'C');
    $pdf->Cell($w_fnac,6,$row['fecha_nacimiento'],1,0,'C');
    $pdf->Cell($w_apoderado,6,fixEncoding($row['apoderado']),1,0,'L');
    $pdf->Cell($w_tel,6,$row['telefono'],1,0,'C');
    $pdf->Cell($w_correo,6,fixEncoding($row['correo']),1,1,'L');
}

$pdf->Output("I", "Lista_$grado.pdf");
?>
