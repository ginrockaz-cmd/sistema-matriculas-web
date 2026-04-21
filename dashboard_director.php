<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'director') {
    header("Location: login.php");
    exit;
}
?>
<h1>Bienvenido Director <?= $_SESSION['nombre'] ?></h1>
<a href="students.php">Gestionar Alumnos</a><br>
<a href="logout.php">Cerrar sesión</a>
