<?php
session_start();
require 'config.php';
$mysqli = db_connect();

// --- Verificar sesión ---
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// --- Restricción de roles ---
$rol = $_SESSION['rol'];
if (!in_array($rol, ['director', 'administrador', 'secretaria'])) {
    header("Location: login.php");
    exit;
}

// --- Crear (Agregar alumno) con ID continuo ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $n = $mysqli->real_escape_string($_POST['apellido_nombre'] ?? '');
    $dni = $mysqli->real_escape_string($_POST['dni'] ?? '');
    $fn = $_POST['fecha_nacimiento'] ?: null;
    $apo = $mysqli->real_escape_string($_POST['apoderado'] ?? '');
    $telef = $mysqli->real_escape_string($_POST['telefono'] ?? '');
    $correo = $mysqli->real_escape_string($_POST['correo'] ?? '');
    $grado = intval($_POST['grado_id'] ?? 0) ?: null;

    // ✅ Si no se seleccionó grado, usar o crear el grado "Otros"
    if (empty($grado)) {
        $res_otro = $mysqli->query("SELECT id FROM grados WHERE nombre='Otros' LIMIT 1");
        if ($res_otro && $res_otro->num_rows > 0) {
            $grado_data = $res_otro->fetch_assoc();
            $grado = intval($grado_data['id']);
        } else {
            $mysqli->query("INSERT INTO grados (nombre) VALUES ('Otros')");
            $grado = $mysqli->insert_id;
        }
    }

    // Obtener el menor ID disponible (busca el primer "gap" en la secuencia)
    $getId = $mysqli->query("SELECT (t1.id + 1) AS next_id
                             FROM alumnos t1
                             LEFT JOIN alumnos t2 ON t1.id + 1 = t2.id
                             WHERE t2.id IS NULL
                             ORDER BY t1.id ASC
                             LIMIT 1");

    if ($getId && $getId->num_rows > 0) {
        $row = $getId->fetch_assoc();
        $next_id = intval($row['next_id']);
        // Si no existe alumno (tabla vacía) ajustar a 1
        if ($next_id <= 0) $next_id = 1;
    } else {
        // tabla vacía -> insertar id = 1
        $next_id = 1;
    }

    // Verificar que next_id no exista por si hay casos raros
    $check = $mysqli->query("SELECT id FROM alumnos WHERE id = {$next_id} LIMIT 1");
    if ($check && $check->num_rows > 0) {
        // Si existe (muy improbable) usar MAX(id)+1
        $r = $mysqli->query("SELECT MAX(id) AS m FROM alumnos")->fetch_assoc();
        $next_id = intval($r['m']) + 1;
    }

    // Insertar con ese ID (usar prepared statement)
    $stmt = $mysqli->prepare("INSERT INTO alumnos (id, apellido_nombre, dni, fecha_nacimiento, apoderado, telefono, correo, grado_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error al preparar la consulta: " . $mysqli->error);
    }
    // Para fecha nullable, pasar NULL como null en bind_param no funciona; tratamos fecha como string o null.
    $fn_param = $fn ?: null;
    // bind_param requiere tipos: i s s s s s s i  -> pero grado puede ser NULL, manejamos con i y pasar NULL como null int
    // Para permitir nulls en bind_param, pasar variable con null está bien.
    $stmt->bind_param("issssssi", $next_id, $n, $dni, $fn_param, $apo, $telef, $correo, $grado);
    if (!$stmt->execute()) {
        die("Error de registrar al estudiante: " . $stmt->error);
    }
    $stmt->close();

    header('Location: students.php?tab=lista&msg=added');
    exit;
}

// --- Eliminar ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM alumnos WHERE id=$id") or die($mysqli->error);
    header('Location: students.php?tab=lista&msg=deleted');
    exit;
}

// --- Actualizar ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    $n = $mysqli->real_escape_string($_POST['apellido_nombre'] ?? '');
    $dni = $mysqli->real_escape_string($_POST['dni'] ?? '');
    $fn = $_POST['fecha_nacimiento'] ?: null;
    $apo = $mysqli->real_escape_string($_POST['apoderado'] ?? '');
    $telef = $mysqli->real_escape_string($_POST['telefono'] ?? '');
    $correo = $mysqli->real_escape_string($_POST['correo'] ?? '');
    $grado = intval($_POST['grado_id'] ?? 0) ?: null;

    // ✅ Si no se seleccionó grado, mover a “Otros”
    if (empty($grado)) {
        $res_otro = $mysqli->query("SELECT id FROM grados WHERE nombre='Otros' LIMIT 1");
        if ($res_otro && $res_otro->num_rows > 0) {
            $grado_data = $res_otro->fetch_assoc();
            $grado = intval($grado_data['id']);
        } else {
            $mysqli->query("INSERT INTO grados (nombre) VALUES ('Otros')");
            $grado = $mysqli->insert_id;
        }
    }

    $mysqli->query("UPDATE alumnos SET 
        apellido_nombre='{$n}', dni='{$dni}', fecha_nacimiento=".($fn?"'{$fn}'":"NULL").",
        apoderado='{$apo}', telefono='{$telef}', correo='{$correo}', grado_id=".($grado?"{$grado}":"NULL")."
        WHERE id={$id}") or die($mysqli->error);
    header('Location: students.php?tab=lista&msg=updated');
    exit;
}

// --- LEER grados y alumnos ---
$grados = $mysqli->query("SELECT * FROM grados ORDER BY id ASC");

// --- Si viene con ?edit=, traer alumno específico ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $q = $mysqli->query("SELECT * FROM alumnos WHERE id={$id_edit}");
    if ($q && $q->num_rows > 0) {
        $edit_data = $q->fetch_assoc();
    }
}

// Control de pestañas (tabs)
$tab = $_GET['tab'] ?? 'lista';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión de Estudiantes</title>
<link rel="stylesheet" href="styles.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

/* ======= ESTILOS GENERALES ======= */
body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(135deg, #084673ff, #ffffff);
  margin: 0;
  padding: 40px;
  color: #1e293b;
}

.container {
  background: #ffffff;
  padding: 35px;
  border-radius: 14px;
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
  max-width: 1250px;
  margin: auto;
  border-top: 6px solid #0d47a1;
}

h1 {
  font-size: 28px;
  color: #0d47a1;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}

h2 {
  color: #263238;
  border-left: 5px solid #fbc02d;
  padding-left: 10px;
  margin-top: 25px;
}

hr {
  border: none;
  border-top: 2px solid #236aa0ff;
  margin: 20px 0;
}

/* ======= BOTONES ======= */
.btn {
  display: inline-block;
  padding: 8px 14px;
  font-weight: 500;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s ease-in-out;
  cursor: pointer;
}
.btn:hover { opacity: 0.9; }

.btn.edit { background: #2e7d32; color: white; }
.btn.delete { background: #c62828; color: white; }
.btn-download { background: #6771bfff; color: white; margin-left: 8px; }

/* ======= BOTONES MODERNOS ======= */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 18px;
  font-weight: 600;
  border-radius: 8px;
  text-decoration: none;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.25s ease-in-out;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  border: none;
}

/* 🔹 Botón principal (Agregar / Guardar) */
.btn-primary {
  background: linear-gradient(135deg, #f4bf00ff, #ff9900ff);
  color: #fff;
  letter-spacing: 0.3px;
}
.btn-primary:hover {
  background: linear-gradient(135deg, #1565c0, #1e88e5);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(21, 101, 192, 0.35);
}

/* 🔸 Botón cancelar */
.btn-cancel {
  background: linear-gradient(135deg, #d70909ff, #c00076ff);
  color: white;
}
.btn-cancel:hover {
  background: linear-gradient(135deg, #4b5563, #374151);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(75, 85, 99, 0.3);
}

/* 🔹 Botón volver al inicio */
.btn-home {
  background: linear-gradient(135deg, #fbc02d, #fdd835);
  color: #0d47a1;
  font-weight: 700;
  padding: 10px 20px;
  border-radius: 50px;
  box-shadow: 0 3px 10px rgba(251, 192, 45, 0.4);
  transition: all 0.3s ease;
}
.btn-home:hover {
  background: linear-gradient(135deg, #ffeb3b, #fbc02d);
  transform: scale(1.05);
  box-shadow: 0 5px 14px rgba(251, 192, 45, 0.6);
}

/* 🔹 Íconos dentro de botones */
.btn i {
  margin-right: 6px;
  font-size: 16px;
}

/* ======= NAVEGACIÓN DE PESTAÑAS ======= */
.nav-tabs {
  margin-top: 20px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.nav-tabs a {
  padding: 10px 20px;
  border-radius: 8px;
  background: #e3f2fd;
  color: #0d47a1;
  font-weight: 600;
  text-decoration: none;
  transition: 0.3s;
  box-shadow: 0 2px 6px rgba(13,71,161,0.15);
}

.nav-tabs a:hover {
  background: #bbdefb;
}

.nav-tabs a.active {
  background: #0d47a1;
  color: white;
  box-shadow: 0 3px 8px rgba(13,71,161,0.3);
}

/* ======= TABLA DE LISTA ======= */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size: 14px;
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 10px 8px;
  border: 1px solid #e0e0e0;
}

th {
  background: #fbc02d;
  color: #0d47a1;
  text-align: center;
  font-weight: 600;
}

tr:nth-child(even) { background: #f9f9f9; }

tr:hover {
  background: #e3f2fd;
  transition: 0.2s;
}

/* ======= FORMULARIO ======= */
.student-form {
  background: #fafafa;
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  padding: 25px;
  margin-top: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 18px;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 6px;
  color: #0d47a1;
}

.form-group input,
.form-group select {
  border: 1px solid #b0bec5;
  border-radius: 8px;
  padding: 10px;
  font-size: 14px;
  background: white;
  transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #1565c0;
  box-shadow: 0 0 6px rgba(21,101,192,0.25);
}

.form-actions {
  margin-top: 20px;
  display: flex;
  gap: 12px;
}

/* ======= ACORDEÓN (LISTA POR GRUPOS) ======= */
.accordion-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 12px;
}

.accordion-header {
  background: linear-gradient(90deg, #0d47a1, #1565c0);
  color: white;
  font-weight: 600;
  padding: 12px 16px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.accordion-header:hover {
  background: #1565c0;
}

.accordion-content {
  display: none;
  background: #ffffff;
  padding: 14px;
}

/* ======= BÚSQUEDA ======= */
.search-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}

#searchBox {
  flex: 1;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #90a4ae;
  font-size: 14px;
}

#searchBox:focus {
  border-color: #0d47a1;
  outline: none;
  box-shadow: 0 0 6px rgba(13,71,161,0.25);
}

#suggestions {
  background: white;
  border: 1px solid #bbdefb;
  border-radius: 8px;
  max-height: 250px;
  overflow-y: auto;
}

#suggestions div {
  padding: 8px 10px;
  cursor: pointer;
}

#suggestions div:hover {
  background: #e3f2fd;
}

/* ======= RESULTADO DE BÚSQUEDA ======= */
#result {
  margin-top: 20px;
}

#result div {
  border: 1px solid #e0e0e0;
  background: #fafafa;
  padding: 16px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

#result h3 {
  margin-top: 0;
  color: #0d47a1;
}

/* ======= RESPONSIVE ======= */
@media (max-width: 768px) {
  .container { padding: 18px; }
  table { font-size: 12px; }
  .nav-tabs a { flex: 1; text-align: center; }
  .accordion-header { flex-direction: column; align-items: flex-start; gap: 6px; }
}
</style>

</head>
<body>
<div class="container">
<h1>👩‍🎓 Gestión de Estudiantes</h1>
<a href="dashboard.php" class="btn-home">←🏠 Volver al Inicio</a>

<div class="nav-tabs" style="margin-top:15px;">
    <a href="?tab=lista" class="<?php echo ($tab=='lista'?'active':'')?>">📋 Lista de Estudiantes</a>
    <a href="?tab=agregar" class="<?php echo ($tab=='agregar'?'active':'')?>">➕ Agregar / Editar Estudiante</a>
    <a href="?tab=consultar" class="<?php echo ($tab=='consultar'?'active':'')?>">🔍 Buscar Estudiante</a>
</div>

<hr>

<?php if(isset($_GET['msg'])): ?>
<p style="color:green;">
<?php
if($_GET['msg']=='added') echo "✅ Estudiante agregado correctamente.";
if($_GET['msg']=='updated') echo "✏️ Estudiante actualizado correctamente.";
if($_GET['msg']=='deleted') echo "🗑️ Estudiante eliminado correctamente.";
?>
</p>
<?php endif; ?>

<?php if($tab=='lista'): ?>
    <h2>Lista de Estudiantes</h2>
    <div class="accordion">
    <?php
        // Mostrar todos los grados, incluyendo “Otros”
    if ($grados && $grados->num_rows > 0) {
        while ($g = $grados->fetch_assoc()) {
            $gid = intval($g['id']);
            echo "<div class='accordion-item'>
                    <div class='accordion-header' onclick='toggleAccordion(this)'>📘 Grupo: " . htmlspecialchars($g['nombre']) . "
                    <a href='export_pdf.php?grado_id={$gid}' target='_blank' class='btn-download'>⬇️ Descargar PDF</a></div>
                    <div class='accordion-content'>";

            $gid = intval($g['id']);
            $res = $mysqli->query("SELECT a.*, g.nombre AS grado FROM alumnos a 
                                   LEFT JOIN grados g ON a.grado_id=g.id 
                                   WHERE a.grado_id = {$gid} 
                                   ORDER BY a.apellido_nombre ASC");

            echo "<table>
                  <tr><th>ID</th><th>Estudiante</th><th>DNI</th><th>Fecha Nac</th><th>Apoderado</th><th>Teléfono</th><th>Correo</th><th>Acciones</th></tr>";

            if ($res && $res->num_rows > 0) {
                while ($r = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$r['id']}</td>
                            <td>".htmlspecialchars($r['apellido_nombre'])."</td>
                            <td>{$r['dni']}</td>
                            <td>{$r['fecha_nacimiento']}</td>
                            <td>".htmlspecialchars($r['apoderado'])."</td>
                            <td>{$r['telefono']}</td>
                            <td>{$r['correo']}</td>
                            <td>
                                <a href='?tab=agregar&edit={$r['id']}' class='btn edit'>Editar</a>
                                <a href='?tab=lista&delete={$r['id']}' class='btn delete' onclick=\"return confirm('¿Eliminar al estudiante?')\">Eliminar</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center;'>No hay estudiantes en este grupo.</td></tr>";
            }
            echo "</table></div></div>";
        }
    }

    // ✅ Mostrar también alumnos sin grado asignado (grado_id NULL)
    $resSinGrado = $mysqli->query("SELECT * FROM alumnos WHERE grado_id IS NULL ORDER BY apellido_nombre ASC");
    if ($resSinGrado && $resSinGrado->num_rows > 0) {
        echo "<div class='accordion-item'>
                <div class='accordion-header' onclick='toggleAccordion(this)'>📗 Grupo: Otros</div>
                <div class='accordion-content'><table>
                <tr><th>ID</th><th>Estudiante</th><th>DNI</th><th>Fecha Nac</th><th>Apoderado</th><th>Teléfono</th><th>Correo</th><th>Acciones</th></tr>";
        while ($r = $resSinGrado->fetch_assoc()) {
            echo "<tr>
                    <td>{$r['id']}</td>
                    <td>".htmlspecialchars($r['apellido_nombre'])."</td>
                    <td>{$r['dni']}</td>
                    <td>{$r['fecha_nacimiento']}</td>
                    <td>".htmlspecialchars($r['apoderado'])."</td>
                    <td>{$r['telefono']}</td>
                    <td>{$r['correo']}</td>
                    <td>
                        <a href='?tab=agregar&edit={$r['id']}' class='btn edit'>Editar</a>
                        <a href='?tab=lista&delete={$r['id']}' class='btn delete' onclick=\"return confirm('¿Eliminar al estudiante?')\">Eliminar</a>
                    </td>
                  </tr>";
        }
        echo "</table></div></div>";
    }
    ?>
    </div>

    <script>
    function toggleAccordion(header) {
        const content = header.nextElementSibling;
        const allContents = document.querySelectorAll('.accordion-content');
        allContents.forEach(c => {
            if (c !== content) c.style.display = 'none';
        });
        content.style.display = (content.style.display === 'block') ? 'none' : 'block';
    }
    </script>

<?php elseif($tab=='agregar'): ?>

    <h2 style="margin-bottom:20px;"><?= $edit_data ? '✏️ Editar Estudiante' : '➕ Agregar Estudiante' ?></h2>

    <form method="post" class="student-form" autocomplete="off">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= intval($edit_data['id']) ?>">
    <?php endif; ?>

    <div class="form-grid">
        <div class="form-group">
            <label>👤 Apellidos y Nombres</label>
            <input name="apellido_nombre" value="<?= htmlspecialchars($edit_data['apellido_nombre'] ?? '') ?>" placeholder="Ej. Fernández García Luis" required>
        </div>
        <div class="form-group">
            <label>🆔 DNI</label>
            <input name="dni" value="<?= htmlspecialchars($edit_data['dni'] ?? '') ?>" placeholder="Ej. 72984561">
        </div>
        <div class="form-group">
            <label>📅 Fecha de Nacimiento</label>
            <input name="fecha_nacimiento" value="<?= htmlspecialchars($edit_data['fecha_nacimiento'] ?? '') ?>" type="date">
        </div>
       <div class="form-group" style="position:relative;">
    <label>👨‍👩‍👧 Apoderado</label>
    <input 
        type="text" 
        name="apoderado" 
        id="apoderadoSearch"
        value="<?= htmlspecialchars($edit_data['apoderado'] ?? '') ?>" 
        placeholder="Escriba nombre del apoderado..." 
        autocomplete="off">

    <div id="apoderadoResults"
         style="
            position:absolute;
            top:70px;
            left:0;
            width:100%;
            background:white;
            border:1px solid #ccc;
            border-radius:6px;
            display:none;
            max-height:220px;
            overflow-y:auto;
            z-index:9999;
         ">
    </div>
</div>
        <div class="form-group">
            <label>📞 Teléfono</label>
            <input name="telefono" value="<?= htmlspecialchars($edit_data['telefono'] ?? '') ?>" placeholder="Ej. 987654321">
        </div>
        <div class="form-group">
            <label>📧 Correo Electrónico</label>
            <input name="correo" value="<?= htmlspecialchars($edit_data['correo'] ?? '') ?>" placeholder="Ej. alumno@correo.com" type="email">
        </div>
        <div class="form-group">
            <label>🏫 Grupo</label>
            <select name="grado_id">
                <option value="">-- Seleccionar Grupo --</option>
                <?php
                $gq = $mysqli->query("SELECT * FROM grados ORDER BY id ASC");
                while ($gr = $gq->fetch_assoc()) {
                    $sel = ($edit_data && $edit_data['grado_id']==$gr['id']) ? 'selected' : '';
                    echo "<option value='".intval($gr['id'])."' $sel>".htmlspecialchars($gr['nombre'])."</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button name="<?= $edit_data ? 'update_student' : 'add_student' ?>" type="submit" class="btn-primary">
            <?= $edit_data ? '💾 Guardar cambios' : '➕ Agregar estudiante' ?>
        </button>
        <a href="students.php?tab=lista" class="btn-cancel">Cancelar</a>
    </div>
    </form>

<?php elseif($tab=='consultar'): ?>

    <h2></h2>

    <div class="search-row">
      <input type="text" id="searchBox" placeholder="Buscar por apellidos o nombre..." autocomplete="off" style="flex:1; padding:10px; border:1px solid #ccc; border-radius:6px;">
      <button id="searchBtn" class="btn">🔍 Busqueda</button>
    </div>

    <div id="suggestions" style="background:#fff;border:1px solid #ccc;max-height:240px;overflow-y:auto;position:relative;width:100%;"></div>
    <div id="result" style="margin-top:20px;"></div>

    <script>
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    const suggestions = document.getElementById('suggestions');
    const resultBox = document.getElementById('result');

    searchBox.addEventListener('keyup', () => {
        const q = searchBox.value.trim();
        if(q.length < 1){ suggestions.innerHTML=''; return; }

        fetch('students_search.php?q='+encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            suggestions.innerHTML = data.length ?
                data.map(a => `<div style="padding:8px;border-bottom:1px solid #f1f1f1;cursor:pointer;" onclick='selectStudent(${JSON.stringify(a)})'>${a.apellido_nombre}</div>`).join('')
                : '<div style="padding:8px;color:#888;">Sin resultados</div>';
        });
    });

    searchBtn.addEventListener('click', () => {
        const q = searchBox.value.trim();
        if(q.length < 1){ alert('Por favor, ingresa un nombre o apellido.'); return; }

        fetch('students_search.php?q='+encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                resultBox.innerHTML = "<p style='color:red;'>No se encontró ningún estudiante con ese nombre.</p>";
            } else {
                const a = data[0];
                selectStudent(a);
            }
        });
    });

    function selectStudent(a){
        suggestions.innerHTML='';
        searchBox.value = a.apellido_nombre;

        resultBox.innerHTML = `
            <div style="border:1px solid #ddd;padding:12px;background:#fafafa;border-radius:8px;">
                <h3 style="margin-top:0;">Información del Estudiante</h3>
                <p><b>Nombre:</b> ${a.apellido_nombre}</p>
                <p><b>DNI:</b> ${a.dni ?? ''}</p>
                <p><b>Fecha Nac.:</b> ${a.fecha_nacimiento ?? ''}</p>
                <p><b>Apoderado:</b> ${a.apoderado ?? ''}</p>
                <p><b>Teléfono:</b> ${a.telefono ?? ''}</p>
                <p><b>Correo:</b> ${a.correo ?? ''}</p>
                <p><b>Grupo:</b> ${a.grado ?? ''}</p>
                <div style="margin-top:8px;">
                    <a href='students.php?tab=agregar&edit=${a.id}' class='btn edit'>✏️ Editar</a>
                    <a href='students.php?tab=lista&delete=${a.id}' class='btn delete' onclick="return confirm('¿Eliminar al estudiante?')">🗑️ Eliminar</a>
                    <button class='btn estado' onclick='modificarEstado(${a.id})'>⚙️ Modificar Estado</button>
                </div>
            </div>
        `;
    }

    function modificarEstado(alumnoId){
    // Mostrar prompt para elegir estado
    const estado = prompt("Ingrese nuevo estado: activo, retirado o fallecido").toLowerCase();

    if(!['activo','retirado','fallecido'].includes(estado)){
        alert("Estado inválido. Debe ser: activo, retirado o fallecido.");
        return;
    }

    // Enviar cambio via AJAX
    fetch('update_estado.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({alumno_id: alumnoId, estado: estado})
    })
    .then(res => res.json())
    .then(resp => {
        if(resp.success){
            alert("Estado actualizado correctamente.");
            // Opcional: actualizar el botón o info en pantalla
            selectStudent({...resp.updatedStudent}); 
        } else {
            alert("Error al actualizar estado: " + resp.error);
        }
    })
    .catch(err => alert("Error de conexión: " + err));
}

    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('apoderadoSearch');
    const results = document.getElementById('apoderadoResults');

    input.addEventListener('keyup', () => {
        const q = input.value.trim();

        if(q.length < 2){
            results.style.display = 'none';
            return;
        }

        fetch('apoderados_search.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                results.innerHTML = '';

                if(data.length === 0){
                    results.style.display = 'none';
                    return;
                }

                data.forEach(apo => {
                    const item = document.createElement('a');
                    item.href = "#";
                    item.style.display = "block";
                    item.style.padding = "8px 10px";
                    item.style.textDecoration = "none";
                    item.style.color = "#333";
                    item.style.borderBottom = "1px solid #eee";

                    item.textContent = apo.apoderado;

                    item.addEventListener('click', e => {
                        e.preventDefault();
                        input.value = apo.apoderado;
                        results.innerHTML = '';
                        results.style.display = 'none';
                    });

                    results.appendChild(item);
                });

                results.style.display = 'block';
            });
    });

    // cerrar al hacer clic fuera
    document.addEventListener('click', e => {
        if(!results.contains(e.target) && e.target !== input){
            results.style.display = 'none';
        }
    });

});
</script>
<?php endif; ?>
</div>
</body>
</html>
