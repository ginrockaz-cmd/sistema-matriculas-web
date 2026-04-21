<?php 
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

$edit_data = null;

/* =======================================================
   FUNCIONES AUXILIARES
======================================================= */
function obtenerMontosPorGrado($grado) {
    $inicial   = ["ET","I3","I4","I5"];
    $primaria  = ["P1","P2","P3A","P3B","P4A","P4B","P5A","P5B","P6"];
    $secundaria = ["S1","S2","S3","S4","S5"];

    if (in_array($grado, $inicial)) return ["matricula"=>230, "pension"=>330];
    if (in_array($grado, $primaria)) return ["matricula"=>310, "pension"=>385];
    if (in_array($grado, $secundaria)) return ["matricula"=>320, "pension"=>395];

    return ["matricula"=>0, "pension"=>0];
}

function pagoEstaVencido($mes_num, $anio_escolar) {
    if ($mes_num < 1 || $mes_num > 12) return false;
    $fecha_vencimiento = date("Y-m-d", strtotime("$anio_escolar-$mes_num-01 +1 month"));
    return (date('Y-m-d') > $fecha_vencimiento);
}

/* =======================================================
   REGISTRAR NUEVO PAGO
======================================================= */
if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['add_payment'])) {
    $alumno_id = intval($_POST['alumno_id']);
    $mes = $mysqli->real_escape_string($_POST['mes']);
    $fecha_pago = $_POST['fecha_pago'];
    $estado_manual = $_POST['estado'] ?? null;

    $anio_escolar = intval(date('Y', strtotime($fecha_pago)));

    $res = $mysqli->query("SELECT g.nombre AS grado FROM alumnos a
                           LEFT JOIN grados g ON a.grado_id=g.id
                           WHERE a.id=$alumno_id LIMIT 1");
    $grado = ($res && $res->num_rows>0) ? $res->fetch_assoc()['grado'] : "";
    $montos = obtenerMontosPorGrado($grado);

    // Determinar el concepto según el mes
    $concepto = ($mes=="Enero" || $mes=="Febrero") ? "Matrícula" : "Pensión";

    // Tomar el monto ingresado por el usuario, si lo hay; si no, usar monto por defecto
    $monto_usuario = isset($_POST['monto']) && $_POST['monto'] !== '' ? floatval($_POST['monto']) : null;
    $monto = $monto_usuario ?? (($concepto=="Matrícula") ? $montos["matricula"] : $montos["pension"]);

    // Determinar estado
    if (in_array($estado_manual, ['Pagado','Pendiente'])) {
        $estado = $estado_manual;
    } else {
        $mes_num = intval(date('n', strtotime("1 $mes")));
        $estado = pagoEstaVencido($mes_num, $anio_escolar) ? "Vencido" : "Pendiente";
    }

    // ⚡ Aquí insertamos directamente, sin verificación por mes
    $stmt = $mysqli->prepare("INSERT INTO pagos 
        (alumno_id, mes_pago, monto, fecha_pago, estado, observaciones,
         metodo_pago, detalle, apoderado, codigo_operacion, anio_escolar)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isdsssssssi",
        $alumno_id,
        $mes,
        $monto,
        $fecha_pago,
        $estado,
        $_POST['observaciones'],
        $_POST['metodo_pago'],
        $_POST['detalle'],
        $_POST['apoderado'],
        $_POST['codigo_operacion'],
        $anio_escolar
    );
    $stmt->execute();
    $stmt->close();

    header("Location: payments.php?msg=added");
    exit;
}

/* =======================================================
   EDITAR PAGO
======================================================= */
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $res = $mysqli->query("SELECT * FROM pagos WHERE id=$id LIMIT 1");
    if ($res && $res->num_rows>0) $edit_data = $res->fetch_assoc();
}

/* =======================================================
   ACTUALIZAR PAGO
======================================================= */
if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['update_payment'])) {
    $id = intval($_POST['id']);
    if($id <= 0){
        die("Error: ID de pago inválido.");
    }

    $anio_escolar = intval(date('Y', strtotime($_POST['fecha_pago'])));
    $alumno_id    = intval($_POST['alumno_id']); // ✅ Tomar el nuevo alumno seleccionado

    $stmt = $mysqli->prepare("UPDATE pagos SET 
        alumno_id=?, 
        mes_pago=?, 
        monto=?, 
        fecha_pago=?, 
        estado=?, 
        observaciones=?, 
        metodo_pago=?, 
        detalle=?, 
        apoderado=?, 
        codigo_operacion=?, 
        anio_escolar=?
        WHERE id=?");

    $stmt->bind_param(
        "isdssssssiii",
        $alumno_id,           // ✅ actualizar el alumno
        $_POST['mes'],
        $_POST['monto'],
        $_POST['fecha_pago'],
        $_POST['estado'],
        $_POST['observaciones'],
        $_POST['metodo_pago'],
        $_POST['detalle'],
        $_POST['apoderado'],
        $_POST['codigo_operacion'],
        $anio_escolar,
        $id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: payments.php?msg=updated");
    exit;
}

/* =======================================================
   ELIMINAR PAGO
======================================================= */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if($id > 0){
        $mysqli->query("DELETE FROM pagos WHERE id=$id LIMIT 1");
    }
    header("Location: payments.php?msg=deleted");
    exit;
}

/* =======================================================
   LISTAR PAGOS
======================================================= */
$res = $mysqli->query("
    SELECT p.*, a.apellido_nombre
    FROM pagos p
    JOIN alumnos a ON a.id=p.alumno_id
    ORDER BY a.apellido_nombre ASC, p.anio_escolar DESC,
        FIELD(p.mes_pago,
            'Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')
");

// Si se pasó por GET (desde búsqueda o edición)
$alumno_actual = intval($_GET['alumno_id'] ?? 0);

// Si no, tomar el primer alumno registrado
if ($alumno_actual == 0 && $res && $res->num_rows > 0) {
    $first_row = $res->fetch_assoc();
    $alumno_actual = intval($first_row['alumno_id']);
    // Rewind del result set para no perder datos
    $res->data_seek(0);
}

?>
<!-- Aquí sigue todo tu HTML -->
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Gestión de Pagos | I.E.P. Divina Misericordia</title>
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(to right, #6fb1fc, #4364f7, #3f51b5);
    margin: 0; padding: 0;
    color: #333;
}
.container {
    background: #fff;
    margin: 50px auto;
    padding: 30px 40px;
    border-radius: 15px;
    max-width: 1100px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.header { display: flex; justify-content: space-between; align-items: center; }
.header h1 { color: #3f51b5; margin: 0; }
.header a {
    background: #3f51b5; color: white; padding: 8px 14px;
    border-radius: 8px; text-decoration: none; font-weight: 600;
}
form {
    background: #f9f9ff; padding: 20px; border-radius: 10px; margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
form label { font-weight: 600; }
form select, form input, textarea {
    width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;
    margin-top: 5px; margin-bottom: 15px;
}
form button {
    background: #3f51b5; color: #fff; border: none; padding: 10px 18px;
    border-radius: 6px; cursor: pointer; font-weight: 600;
}
form button:hover { background: #5c6bc0; }
.btn-cancel {
    background: #f44336; color: #fff; border: none;
    padding: 10px 18px; border-radius: 6px; cursor: pointer; font-weight: 600;
}
.btn-cancel:hover { background: #d32f2f; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th {
    background: #3f51b5; color: white; padding: 10px; text-align: center;
}
td {
    background: #fafafa; padding: 8px; text-align: center;
    border-bottom: 1px solid #eee;
}
.btn-edit, .btn-delete {
    display: inline-block; padding: 6px 10px; border-radius: 6px; text-decoration: none;
    color: white; font-size: 13px; font-weight: bold;
}
.btn-edit { background: #4caf50; }
.btn-edit:hover { background: #388e3c; }
.btn-delete { background: #f44336; }
.btn-delete:hover { background: #c62828; }
.success { color: green; font-weight: bold; }
</style>
</head>
<body>
<div class="container">
<div class="header">
    <h1>💰 Gestión de Pagos</h1>
    <a href="dashboard.php">← Volver al Inicio</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg']=='added'): ?><p class="success">✅ Pago registrado correctamente.</p>
    <?php elseif ($_GET['msg']=='updated'): ?><p class="success">✅ Cambios guardados correctamente.</p>
    <?php elseif ($_GET['msg']=='deleted'): ?><p class="success">🗑️ Registro eliminado correctamente.</p>
    <?php endif; ?>
<?php endif; ?>

<h2><?= $edit_data ? '✏️ Editar Pago' : '➕ Registrar nuevo pago' ?></h2>
<form method="post">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?=$edit_data['id']?>">
        <input type="hidden" name="alumno_id" value="<?=$edit_data['alumno_id']?>">
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

/* Botón Descargar PDF para Pagos Registrados */
.btn-download {
    display: inline-flex;                  /* centrado vertical y horizontal */
    align-items: center;
    justify-content: center;
    padding: 10px 20px;                    /* espacio interno */
    background: linear-gradient(135deg, #1976d2, #4a90e2); /* degradado azul */
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0 6px 15px rgba(25,118,210,0.3);
    transition: all 0.3s ease;
    margin-bottom: 15px;                   /* separación del contenido de abajo */
}

.btn-download:hover {
    background: linear-gradient(135deg, #125ca1, #3b78d1);
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(25,118,210,0.35);
    opacity: 0.95;
}

.btn-download:active {
    transform: translateY(0);
    box-shadow: 0 5px 12px rgba(25,118,210,0.25);
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

                // Mostrar solo nombre completo
                data.forEach(student => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.textContent = `${student.apellido_nombre} (${student.grado ?? '-'})`;
                    item.addEventListener('click', e => {
                        e.preventDefault();
                        alumnoIdInput.value = student.id;
                        input.value = student.apellido_nombre;
                        results.innerHTML = ''; // Limpia la lista
                        results.style.display = 'none'; // Cierra el cuadro
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

    <label>🗓 Mes:</label>
    <select name="mes" required>
        <?php
        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        foreach($meses as $m){
            $sel = ($edit_data && $edit_data['mes_pago']==$m)?'selected':'';
            echo "<option $sel>$m</option>";
        }
        ?>
    </select>

    <label>💵 Monto:</label>
    <input type="number" step="0.01" name="monto" value="<?=$edit_data['monto'] ?? ''?>">

    <label>📅 Fecha:</label>
    <input type="date" name="fecha_pago" value="<?=$edit_data['fecha_pago'] ?? date('Y-m-d')?>">

    <label>📄 Estado:</label>
    <select name="estado" required>
        <?php foreach(['Pagado','Pendiente'] as $e): ?>
        <option value="<?=$e?>" <?=($edit_data && $edit_data['estado']==$e)?'selected':''?>><?=$e?></option>
        <?php endforeach; ?>
    </select>

    <label>🗒 Observaciones:</label>
    <textarea name="observaciones" rows="2"><?=$edit_data['observaciones'] ?? ''?></textarea>

    <hr><h3>💳 Método de Pago</h3>

    <label>Método:</label>
    <select name="metodo_pago" required>
        <?php foreach(['Efectivo','Yape','Transferencia','Depósito'] as $m): ?>
        <option value="<?=$m?>" <?=($edit_data && $edit_data['metodo_pago']==$m)?'selected':''?>><?=$m?></option>
        <?php endforeach; ?>
    </select>

    <label>Detalle:</label>
    <input type="text" name="detalle" value="<?=$edit_data['detalle'] ?? ''?>">

    <label>Código de operación:</label>
    <input type="text" name="codigo_operacion" value="<?=$edit_data['codigo_operacion'] ?? ''?>">

    <?php if ($edit_data): ?>
        <button type="submit" name="update_payment">💾 Guardar Cambios</button>
        <a href="payments.php" class="btn-cancel">❌ Cancelar</a>
    <?php else: ?>
        <button type="submit" name="add_payment">💾 Registrar Pago Completo</button>
    <?php endif; ?>
</form>

<h2>📋 Pagos Registrados</h2>
<?php if ($alumno_actual > 0): ?>
    <a href="payments_pdf.php?alumno_id=<?= $alumno_actual ?>" target="_blank" class="btn-download">
        ⬇️ Descargar PDF
    </a>
<?php endif; ?>
<table>
<tr>
    <th>Estudiante</th><th>Mes</th><th>Monto</th><th>Fecha</th><th>Estado</th>
    <th>Método</th><th>Detalle</th><th>Código</th><th>Observaciones</th><th>Acciones</th>
</tr>
<?php
$anio_actual = '';

if ($res && $res->num_rows > 0):
while ($r = $res->fetch_assoc()):

    if ($alumno_actual != $r['apellido_nombre']) {
        $alumno_actual = $r['apellido_nombre'];
        echo "<tr><td colspan='11' style='background:#e3f2fd;font-weight:bold;text-align:left;padding:8px'>
              👩‍🎓 Estudiante: {$alumno_actual}
              </td></tr>";
        $anio_actual = '';
    }

    if ($anio_actual != $r['anio_escolar']) {
        $anio_actual = $r['anio_escolar'];
        echo "<tr><td colspan='11' style='background:#f5f5f5;font-weight:bold;text-align:left;padding:8px'>
              📅 Año Escolar: {$anio_actual}
              </td></tr>";
    }
?>
<tr>
    <td><?=$r['apellido_nombre']?></td>
    <td><?=$r['mes_pago']?></td>
    <td><?=$r['monto']?></td>
    <td><?=$r['fecha_pago']?></td>
    <td><?=$r['estado']?></td>
    <td><?=$r['metodo_pago'] ?: '—'?></td>
    <td><?=$r['detalle'] ?: '—'?></td>
    <td><?=$r['codigo_operacion'] ?: '—'?></td>
    <td><?=$r['observaciones']?></td>
    <td>
        <a href="payments.php?edit_id=<?=$r['id']?>" class="btn-edit">✏️ Editar</a>
        <a href="payments.php?delete_id=<?=$r['id']?>" class="btn-delete"
           onclick="return confirm('¿Eliminar este registro?');">🗑️ Eliminar</a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr>
    <td colspan="11" style="text-align:center;color:#888;">
        No hay pagos registrados.
    </td>
</tr>
<?php endif; ?>
</table>
</div>
</body>
</html>