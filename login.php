<?php 
session_start();
require 'config.php';
$mysqli = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = md5($_POST['password']); // cifrado igual que en BD

    $result = $mysqli->query("SELECT * FROM usuarios WHERE email='$email' AND password='$password' LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        // Redirección por rol
        if ($user['rol'] == 'director') {
            header("Location: dashboard.php");
        } elseif ($user['rol'] == 'administrador') {
            header("Location: dashboard_admin.php");
        } elseif ($user['rol'] == 'secretaria') {
            header("Location: dashboard_secretaria.php");
        }
        exit;
    } else {
        $error = "❌ Credenciales incorrectas. Intente nuevamente.";
    }
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Login - I.E.P. Divina Misericordia</title>
<link rel="icon" href="logo_dm.png" type="image/png">
<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        height: 100vh;
        background: linear-gradient(135deg, #3f51b5, #2196f3);
        display: flex;
        justify-content: center;
        align-items: center;
        color: #333;
    }

    .login-container {
        background: #fff;
        padding: 40px 35px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 360px;
        text-align: center;
        animation: fadeIn 0.7s ease-in-out;
    }

    .login-container img {
        width: 90px;
        height: 90px;
        margin-bottom: 10px;
    }

    h2 {
        color: #0d47a1;
        margin-bottom: 10px;
    }

    p.sub {
        color: #444;
        font-size: 14px;
        margin-bottom: 25px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: 0.3s;
    }

    input:focus {
        border-color: #2196f3;
        box-shadow: 0 0 8px rgba(33,150,243,0.4);
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(90deg, #3f51b5, #2196f3);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    button:hover {
        background: linear-gradient(90deg, #303f9f, #1976d2);
        transform: scale(1.02);
    }

    .error {
        color: #e53935;
        font-weight: 600;
        background: #ffebee;
        border: 1px solid #ffcdd2;
        padding: 8px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    footer {
        margin-top: 25px;
        font-size: 13px;
        color: #666;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>

<body>
<div class="login-container">
    <img src="logo_dm.png" alt="Logo Divina Misericordia">
    <h2>I.E.P. Divina Misericordia</h2>
    <p class="sub">Sistema de Matrículas y Pensiones</p>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post" autocomplete="off">
        <input type="email" name="email" placeholder="Correo institucional" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <footer>© 2025 - Comas, Carabayllo - Av. Alameda del Pinar</footer>
</div>
</body>
</html>

