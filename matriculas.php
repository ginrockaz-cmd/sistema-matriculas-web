<?php
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

/**
 * Devuelve los montos según grupo (por si lo necesitas más adelante)
 */
function getFeesByGroup($group) {
    $g = strtoupper(trim($group));
    $initial = ['ET','I3','I4','I5'];
    $primaria = ['P1','P2','P3A','P3B','P4A','P4B','P5A','P5B','P6'];
    $secundaria = ['S1','S2','S3','S4','S5'];

    if (in_array($g, $initial)) return ['matricula' => 230.00, 'pension' => 330.00];
    if (in_array($g, $primaria)) return ['matricula' => 310.00, 'pension' => 385.00];
    if (in_array($g, $secundaria)) return ['matricula' => 320.00, 'pension' => 395.00];
    return ['matricula' => 0.00, 'pension' => 0.00];
}

/* ============================================================================ 
   REGISTRAR MATRÍCULA (sin crear cargos en BD)
============================================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_matricula'])) {

    $al = intval($_POST['alumno_id']);
    $anio = intval($_POST['anio_year']);
    $grado = intval($_POST['grado_id'] ?: 0);

    // Validar y formatear fecha
    $fecha = !empty($_POST['fecha_matricula']) ? $_POST['fecha_matricula'] : date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $fecha = date('Y-m-d');
    }

    $estado = 'activo';

    // Obtener ID libre (reciclador automático)
    $getId = $mysqli->query("
        SELECT (t1.id + 1) AS next_id
        FROM matriculas t1
        LEFT JOIN matriculas t2 ON t1.id + 1 = t2.id
        WHERE t2.id IS NULL
        ORDER BY t1.id ASC
        LIMIT 1
    ");

    if ($getId && $getId->num_rows > 0) {
        $row = $getId->fetch_assoc();
        $next_id = intval($row['next_id']);
        if ($next_id <= 0) $next_id = 1;
    } else {
        $next_id = 1;
    }

    // Verificar que el ID esté libre
    $check = $mysqli->query("SELECT id FROM matriculas WHERE id = {$next_id} LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $r = $mysqli->query("SELECT MAX(id) AS m FROM matriculas")->fetch_assoc();
        $next_id = intval($r['m']) + 1;
    }

    // Insertar matrícula
    $stmt = $mysqli->prepare("
        INSERT INTO matriculas (id, alumno_id, anio_year, fecha_matricula, grado_id, estado)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) die("Error al preparar la consulta: " . $mysqli->error);

    // Tipos: i=integer, s=string
    $stmt->bind_param("iiisss", $next_id, $al, $anio, $fecha, $grado, $estado);
    if (!$stmt->execute()) die("Error al registrar matrícula: " . $stmt->error);
    $stmt->close();

    // 🔁 Sincronizar grado actual del alumno
$stmtUpd = $mysqli->prepare("
    UPDATE alumnos 
    SET grado_id = ?
    WHERE id = ?
");
$stmtUpd->bind_param("ii", $grado, $al);
$stmtUpd->execute();
$stmtUpd->close();

    header('Location: matriculas.php?msg=added');
    exit;
}

/* ============================================================================ 
   ELIMINAR MATRÍCULA
============================================================================ */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM matriculas WHERE id=$id") or die($mysqli->error);
    header('Location: matriculas.php?msg=deleted');
    exit;
}

/* ============================================================================ 
   EDITAR MATRÍCULA
============================================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_matricula'])) {
    $id = intval($_POST['id']);
    $al = intval($_POST['alumno_id']);
    $anio = intval($_POST['anio_year']);
    $grado = intval($_POST['grado_id'] ?: 0);
    $estado = $_POST['estado'] ?? 'activo';

    // Validar y formatear fecha
    $fecha = !empty($_POST['fecha_matricula']) ? $_POST['fecha_matricula'] : date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $fecha = date('Y-m-d');
    }

    $stmt = $mysqli->prepare("
        UPDATE matriculas SET 
            alumno_id = ?,
            anio_year = ?,
            fecha_matricula = ?,
            grado_id = ?,
            estado = ?
        WHERE id = ?
    ");
    if (!$stmt) die("Error al preparar la actualización: " . $mysqli->error);

    $stmt->bind_param("iisssi", $al, $anio, $fecha, $grado, $estado, $id);
    if (!$stmt->execute()) die("Error al actualizar matrícula: " . $stmt->error);
    $stmt->close();

    // 🔁 Sincronizar cambio de grado en alumnos
$stmtUpd = $mysqli->prepare("
    UPDATE alumnos 
    SET grado_id = ?
    WHERE id = ?
");
$stmtUpd->bind_param("ii", $grado, $al);
$stmtUpd->execute();
$stmtUpd->close();

    header('Location: matriculas.php?msg=updated');
    exit;
}

/* ============================================================================ 
   OBTENER MATRÍCULA PARA EDITAR
============================================================================ */
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $q = $mysqli->query("SELECT * FROM matriculas WHERE id=$id_edit");
    if ($q && $q->num_rows > 0) {
        $edit_data = $q->fetch_assoc();
    }
}

/* ============================================================================ 
   LISTAR MATRÍCULAS
============================================================================ */
$res = $mysqli->query("
    SELECT m.*, a.apellido_nombre, g.nombre AS grado 
    FROM matriculas m 
    JOIN alumnos a ON m.alumno_id = a.id 
    LEFT JOIN grados g ON m.grado_id = g.id 
    ORDER BY m.id DESC
");
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión de Matrículas | I.E.P. Divina Misericordia</title>
<link rel="stylesheet" href="styles.css">
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(to right, #6fb1fc, #4364f7, #3f51b5);
    margin: 0;
    padding: 0;
    color: #333;
}
.container {
    background: #fff;
    margin: 50px auto;
    padding: 30px 40px;
    border-radius: 15px;
    max-width: 1000px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header h1 {
    color: #3f51b5;
    margin: 0;
}
.header a {
    background: #3f51b5;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}
.header a:hover {
    background: #283593;
}
form {
    background: #f9f9ff;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
form label {
    font-weight: 600;
    color: #333;
}
form select, form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
    margin-bottom: 15px;
    font-size: 14px;
}
form button {
    background: #3f51b5;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}
form button:hover {
    background: #5c6bc0;
}

/* 🔹 Botones de acción */
.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 13px;
    font-weight: 600;
    transition: background 0.3s ease;
    margin-right: 6px;
}
.btn-edit {
    background: #28a745;
}
.btn-edit:hover {
    background: #218838;
}
.btn-delete {
    background: #dc3545;
}
.btn-delete:hover {
    background: #c82333;
}

/* Botón Descargar PDF */
.btn-download {
    display: inline-flex;                  /* centrado vertical y horizontal */
    align-items: center;
    justify-content: center;
    padding: 10px 20px;                    /* espacio interno */
    background: linear-gradient(135deg, #1f59c4, #4364f7);
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0 6px 15px rgba(31,89,196,0.3);
    transition: all 0.3s ease;
    margin-left: 8px;                      /* separación de otros elementos */
}

.btn-download:hover {
    background: linear-gradient(135deg, #283593, #5c6bc0);
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(31,89,196,0.35);
    opacity: 0.95;
}

.btn-download:active {
    transform: translateY(0);
    box-shadow: 0 5px 12px rgba(31,89,196,0.25);
}

.success {
    color: green;
    font-weight: bold;
}
h2 {
    color: #3f51b5;
    border-left: 4px solid #3f51b5;
    padding-left: 10px;
    margin-top: 30px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
th {
    background: #3f51b5;
    color: white;
    padding: 10px;
    text-align: left;
}
td {
    background: #fafafa;
    padding: 8px;
    border-bottom: 1px solid #eee;
}
tr:nth-child(even) td {
    background: #f1f3f9;
}
tr:hover td {
    background: #e8ebff;
}
</style>
</head>
<body>
<div class="container">
<div class="header">
    <h1>📘 Gestión de Matrículas</h1>
    <a href="dashboard.php">← Volver al Inicio</a>
</div>

<?php
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') echo "<p class='success'>✅ Matrícula registrada correctamente.</p>";
    if ($_GET['msg'] == 'updated') echo "<p class='success'>💾 Matrícula actualizada correctamente.</p>";
    if ($_GET['msg'] == 'deleted') echo "<p class='success'>🗑️ Matrícula eliminada correctamente.</p>";
}
?>

<h2><?= $edit_data ? '✏️ Editar Matrícula' : '➕ Registrar nueva matrícula' ?></h2>

<form method="post">
  <?php if ($edit_data): ?>
  <input type="hidden" name="id" value="<?=$edit_data['id']?>">
  <?php endif; ?>

  <!-- 🔹 Campo de Estudiante -->
<label for="studentSearch">👩‍🎓 Estudiante:</label>
<input type="hidden" name="alumno_id" id="alumno_id" value="<?=htmlspecialchars($edit_data['alumno_id'] ?? '')?>">
<input type="text" id="studentSearch" 
       placeholder="Escriba el apellido del estudiante..." 
       autocomplete="off"
       value="<?php
           if ($edit_data) {
               // Obtener el nombre del alumno desde la tabla alumnos
               $id_alumno = intval($edit_data['alumno_id']);
               $res_alu = $mysqli->query("SELECT apellido_nombre FROM alumnos WHERE id = $id_alumno");
               if ($res_alu && $row_alu = $res_alu->fetch_assoc()) {
                   echo htmlspecialchars($row_alu['apellido_nombre']);
               }
           }
       ?>">
<div id="searchResults" class="list-group"></div>

<style>
#searchResults {
    position: absolute;
    width: calc(100% - 2px);
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    max-height: 200px;
    overflow-y: auto;
    margin-top: 2px;
    display: none;
    z-index: 1000;
}
#searchResults a {
    display: block;
    padding: 8px 12px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #eee;
}
#searchResults a:hover {
    background: #f3f3f3;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('studentSearch');
    const results = document.getElementById('searchResults');
    const alumnoIdInput = document.getElementById('alumno_id');

    // Buscar estudiante
    input.addEventListener('keyup', () => {
        const query = input.value.trim();
        if (query.length < 2) {
            results.style.display = 'none';
            return;
        }

        fetch('students_search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                results.innerHTML = '';
                if (data.length === 0) {
                    results.style.display = 'none';
                    return;
                }

                data.forEach(student => {
                    const item = document.createElement('a');
                    item.href = '#';
                    // Mostrar nombre + grupo
                    item.textContent = `${student.apellido_nombre} (${student.grado ?? '-'})`;
                    item.addEventListener('click', e => {
                        e.preventDefault();
                        alumnoIdInput.value = student.id;
                        input.value = student.apellido_nombre;
                        results.innerHTML = '';
                        results.style.display = 'none';
                    });
                    results.appendChild(item);
                });

                results.style.display = 'block';
            })
            .catch(err => console.error('Error al buscar estudiante:', err));
    });

    // Cerrar cuadro al hacer clic fuera
    document.addEventListener('click', e => {
        if (!results.contains(e.target) && e.target !== input) {
            results.style.display = 'none';
        }
    });
});
</script>

      <!-- 🔹 Campos de pago alineados horizontalmente -->
<div class="fila-horizontal">

  <label>🏫 Grupo:</label>
  <select name="grado_id">
    <option value="">-- Seleccionar grupo --</option>
    <?php 
    $gq = $mysqli->query("SELECT id, nombre FROM grados ORDER BY id ASC");
    while($g = $gq->fetch_assoc()){
        $sel = ($edit_data && $edit_data['grado_id'] == $g['id']) ? 'selected' : '';
        echo "<option value='{$g['id']}' $sel>{$g['nombre']}</option>";
    }
    ?>
  </select>

  <label>🗓 Año escolar:</label>
  <input name="anio_year" type="number" placeholder="2025" value="<?= htmlspecialchars($edit_data['anio_year'] ?? '') ?>" required>

  <label>📅 Fecha de matrícula:</label>
  <input name="fecha_matricula" type="date" value="<?= htmlspecialchars($edit_data['fecha_matricula'] ?? '') ?>">

  <label>Estado:</label>
<select name="estado" required>
    <option value="activo" <?= ($edit_data['estado'] ?? '')=='activo' ? 'selected' : '' ?>>Activo</option>
    <option value="retirado" <?= ($edit_data['estado'] ?? '')=='retirado' ? 'selected' : '' ?>>Retirado</option>
    <option value="fallecido" <?= ($edit_data['estado'] ?? '')=='fallecido' ? 'selected' : '' ?>>Fallecido</option>
</select>

  <button name="<?= $edit_data ? 'update_matricula' : 'add_matricula' ?>" type="submit">
    <?= $edit_data ? '💾 Guardar cambios' : '➕ Registrar matrícula' ?>
  </button>
</form>

<h2>📋 Lista de Matrículas Registradas</h2>
<a href="matriculas_pdf.php" target="_blank" class="btn-download">⬇️ Descargar PDF</a>
<table>
<tr>
  <th>Estudiante</th>
  <th>Grupo</th>
  <th>Año</th>
  <th>Fecha de Matrícula</th>
  <th>Acciones</th>
</tr>
<?php 
if ($res && $res->num_rows > 0):
    while($r = $res->fetch_assoc()): ?>
    <tr>
        <td><?=htmlspecialchars($r['apellido_nombre'])?></td>
        <td><?=$r['grado'] ?: '—'?></td>
        <td><?=$r['anio_year']?></td>
        <td><?=$r['fecha_matricula']?></td>
        <td>
            <a href="matriculas.php?edit=<?=$r['id']?>" class="btn-action btn-edit">✏️ Editar</a>
            <a href="matriculas.php?delete=<?=$r['id']?>" class="btn-action btn-delete" onclick="return confirm('¿Eliminar esta matrícula?')">🗑️ Eliminar</a>
        </td>
    </tr>
<?php 
    endwhile;
else: ?>
    <tr><td colspan="6" style="text-align:center;color:#888;">No hay matrículas registradas.</td></tr>
<?php endif; ?>
</table>
</div>
</body>
</html>
