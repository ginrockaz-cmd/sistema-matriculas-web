<?php
session_start();
require 'config.php';
$mysqli = db_connect();

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

// estadísticas
$res = $mysqli->query("SELECT COUNT(*) as total FROM alumnos");
$t = $res->fetch_assoc()['total'] ?? 0;

$res2 = $mysqli->query("SELECT COUNT(*) as pagos FROM pagos WHERE estado='pagado'");
$p = ($res2 && $res2->num_rows) ? $res2->fetch_assoc()['pagos'] : 0;

// NUEVO: contar matrículas registradas
$res3 = $mysqli->query("SELECT COUNT(*) as matriculas FROM matriculas");
$m = ($res3 && $res3->num_rows) ? $res3->fetch_assoc()['matriculas'] : 0;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel Principal - I.E.P. Divina Misericordia</title>
  <link rel="icon" href="logo_dm.png" type="image/png">
  <style>
    :root {
      --azul: #1e40af;
      --celeste: #3b82f6;
      --blanco: #ffffff;
      --gris-claro: #f4f6fb;
      --success: #16a34a;
      --danger: #dc2626;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, var(--celeste), var(--azul));
      margin: 0;
      color: #333;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 30px 0;
    }

    .container {
      width: 90%;
      max-width: 1100px;
      background: var(--blanco);
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
      padding: 30px 40px;
      animation: fadeIn 0.7s ease-in-out;
    }

    /* Encabezado */
    .header {
      display: flex;
      align-items: center;
      gap: 15px;
      border-bottom: 3px solid var(--celeste);
      padding-bottom: 10px;
    }

    .header .logo {
      background: var(--celeste);
      color: white;
      font-weight: bold;
      font-size: 30px;
      padding: 18px 25px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      font-family: 'Poppins', sans-serif;
    }

    .school-title {
      font-size: 22px;
      font-weight: bold;
      color: var(--azul);
      letter-spacing: 0.5px;
    }

    .school-sub {
      font-size: 14px;
      color: #555;
    }

    /* Bienvenida */
    p {
      margin-top: 15px;
      font-size: 15px;
    }
    p a {
      text-decoration: none;
      background: #ef4444;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 13px;
    }
    p a:hover { background: #dc2626; }

    /* Navegación */
    .nav {
      margin-top: 25px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-primary {
      background: var(--celeste);
      color: white;
    }
    .btn-primary:hover {
      background: var(--azul);
    }
    .btn-ghost {
      background: #f3f4f6;
      color: var(--azul);
    }
    .btn-ghost:hover {
      background: #e0e7ff;
      color: var(--azul);
    }

    /* Tarjetas estadísticas */
    .cards {
      display: flex;
      gap: 25px;
      margin-top: 35px;
      flex-wrap: wrap;
    }

    .card {
      flex: 1;
      background: var(--gris-claro);
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .card h3 {
      color: var(--azul);
      margin-bottom: 8px;
    }

    .card p {
      font-size: 30px;
      font-weight: bold;
      color: var(--celeste);
      margin: 0;
    }

    /* Animación */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 13px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo">DM</div>
      <div>
        <div class="school-title">I.E.P. Divina Misericordia</div>
        <div class="school-sub">Comas - Lima, Av. Alameda del Pinar</div>
      </div>
    </div>

    <p>👤 Bienvenido <b><?= htmlspecialchars($nombre) ?></b> (<i><?= htmlspecialchars($rol) ?></i>)
    <a href="logout.php" style="float:right;">🚪 Cerrar sesión</a></p>

    <div class="nav">
      <a href="dashboard.php" class="btn btn-primary">🏠 Inicio</a>
      <a href="students.php" class="btn btn-ghost">👩‍🎓 Estudiantes</a>
      <a href="matriculas.php" class="btn btn-ghost">📘 Matrículas</a>
      <a href="payments.php" class="btn btn-ghost">💰 Pagos</a>
      <a href="modificar_montos_defecto.php" class="btn btn-ghost">💰 Modificar Montos de Matricula/Pensiones</a>
      <a href="estado_cuenta.php" class="btn btn-info">📊 Estado de Cuenta</a>
    </div>

    <div class="cards">
      <div class="card">
        <h3>👨‍🏫 Total de Estudiantes</h3>
        <p><?= $t; ?></p>
      </div>
      <div class="card">
        <h3>📘 Matrículas Registradas</h3>
        <p style="color:#0284c7;"><?= $m; ?></p>
      </div>
      <div class="card">
        <h3>💵 Pagos Registrados</h3>
        <p style="color:var(--success);"><?= $p; ?></p>
      </div>
      <div class="card">
        <h3>📅 Año Académico</h3>
        <p><?= date('Y'); ?></p>
      </div>
    </div>

    <footer>
      © <?= date('Y'); ?> - Sistema de Matrículas y Pensiones | I.E.P. Divina Misericordia - Comas, Lima
    </footer>
  </div>
</body>
</html>
