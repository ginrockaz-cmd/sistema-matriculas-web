<?php
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

$data = json_decode(file_get_contents('php://input'), true);
$alumno_id = intval($data['alumno_id'] ?? 0);
$estado = $data['estado'] ?? '';

if($alumno_id <= 0 || !in_array($estado, ['activo','retirado','fallecido'])){
    echo json_encode(['success'=>false,'error'=>'Datos inválidos']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE matriculas SET estado=? WHERE alumno_id=?");
$stmt->bind_param("si",$estado,$alumno_id);
if($stmt->execute()){
    // Obtener info actualizada para refrescar la pantalla
    $res = $mysqli->query("SELECT * FROM alumnos WHERE id=$alumno_id LIMIT 1");
    $updatedStudent = $res->fetch_assoc();
    echo json_encode(['success'=>true,'updatedStudent'=>$updatedStudent]);
}else{
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}
$stmt->close();
?>
