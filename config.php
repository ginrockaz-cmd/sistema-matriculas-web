<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'matriculas';

function db_connect() {
    global $DB_HOST,$DB_USER,$DB_PASS,$DB_NAME;
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, 3306);
    if ($mysqli->connect_errno) {
        die('DB connect error: '.$mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
?>