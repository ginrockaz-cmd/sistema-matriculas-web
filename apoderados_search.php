<?php
require 'config.php';
$mysqli = db_connect();

$q = trim($_GET['q'] ?? '');
$data = [];

if($q !== ''){
    $q = $mysqli->real_escape_string($q);

    $sql = "
        SELECT DISTINCT apoderado 
        FROM alumnos
        WHERE apoderado LIKE '%$q%'
        AND apoderado <> ''
        ORDER BY apoderado ASC
        LIMIT 20
    ";

    $res = $mysqli->query($sql);

    if($res){
        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }
    }
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
