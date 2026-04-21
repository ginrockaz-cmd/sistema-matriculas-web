<?php
require 'config.php';
$mysqli = db_connect();

$q = trim($_GET['q'] ?? '');
$data = [];

if ($q !== '') {
    $q = mb_strtolower($mysqli->real_escape_string($q));  // <-- CONVIERTE A MINÚSCULAS

    $sql = "SELECT 
                a.id,
                a.apellido_nombre,
                a.dni,
                a.fecha_nacimiento,
                a.apoderado,
                a.telefono,
                a.correo,
                g.nombre AS grado
            FROM alumnos a
            LEFT JOIN grados g ON a.grado_id = g.id
            WHERE LOWER(a.apellido_nombre) LIKE '%$q%'  -- <-- AQUÍ EL CAMBIO
               OR LOWER(a.dni) LIKE '%$q%'
            ORDER BY a.apellido_nombre ASC";

    $res = $mysqli->query($sql);

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);




