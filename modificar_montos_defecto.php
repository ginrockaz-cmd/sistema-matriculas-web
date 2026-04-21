<?php
require 'config.php';
$mysqli = db_connect();
$mysqli->set_charset('utf8mb4');

/* =======================================================
   MODIFICAR MONTOS DE MATRÍCULA Y PENSIONES
======================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_montos'])) {

    $anio_year = intval($_POST['anio_year']);
    $tipo      = $_POST['tipo']; // matricula | pension
    $monto     = floatval($_POST['monto']);
    $alcance   = $_POST['alcance']; // alumno | inicial | primaria | secundaria
    $alumno_id = intval($_POST['alumno_id'] ?? 0);

    $grados = [];
    if ($alcance === 'inicial') $grados = ['ET','I3','I4','I5'];
    elseif ($alcance === 'primaria') $grados = ['P1','P2','P3A','P3B','P4A','P4B','P5A','P5B','P6'];
    elseif ($alcance === 'secundaria') $grados = ['S1','S2','S3','S4','S5'];

    // Preparar statement
$stmt = $mysqli->prepare("
    INSERT INTO modificar_montos_defecto (alumno_id, grado, tipo, anio, monto)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE monto = VALUES(monto)
");

if (!$stmt) {
    die("Error al preparar la consulta: " . $mysqli->error);
}

$null = null; // Variable para alumno_id nulo

if ($alcance === 'alumno' && $alumno_id > 0) {
    // Para un alumno específico
    $stmt->bind_param("issid", $alumno_id, $null, $tipo, $anio_year, $monto);
    $stmt->execute();
} else {
    // Para todos los grados del alcance
    foreach ($grados as $grado) {
        $stmt->bind_param("issid", $null, $grado, $tipo, $anio_year, $monto);
        $stmt->execute();
    }
}

$stmt->close();
    header("Location: modificar_montos_defecto.php?msg=ok");
    exit;

    // Meses según tipo
    if ($tipo === 'matricula') {
        $meses = ['Enero','Febrero'];
    } else {
        $meses = ['Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    }

    // Filtro por alcance
    $whereAlumno = "";
    if ($alcance === 'alumno' && $alumno_id > 0) {
        $whereAlumno = "AND alumno_id = $alumno_id";
    }
    elseif ($alcance === 'inicial') {
        $whereAlumno = "AND alumno_id IN (
            SELECT id FROM alumnos WHERE grupo IN ('ET','I3','I4','I5')
        )";
    }
    elseif ($alcance === 'primaria') {
        $whereAlumno = "AND alumno_id IN (
            SELECT id FROM alumnos WHERE grupo IN ('P1','P2','P3A','P3B','P4A','P4B','P5A','P5B','P6')
        )";
    }
    elseif ($alcance === 'secundaria') {
        $whereAlumno = "AND alumno_id IN (
            SELECT id FROM alumnos WHERE grupo IN ('S1','S2','S3','S4','S5')
        )";
    }

    // Actualizar montos
    foreach ($meses as $mes) {
        $mysqli->query("
            UPDATE pagos
            SET monto = $monto
            WHERE mes_pago = '$mes'
              AND year_pago = $anio_year
              $whereAlumno
        ");
    }

    header("Location: modificar_montos_defecto.php?msg=ok");
    exit;

// Después de insertar en modificar_montos_defecto
$meses = ($tipo === 'matricula') ? ['Enero','Febrero'] : ['Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

foreach ($meses as $mes) {
    $mysqli->query("
        UPDATE pagos
        SET monto = $monto
        WHERE alumno_id " . ($alcance==='alumno' ? "= $alumno_id" : "IN (SELECT id FROM alumnos WHERE grupo IN ('" . implode("','",$grados) . "'))") . "
          AND mes_pago = '$mes'
          AND anio_escolar = $anio_year
    ");
}
}

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Modificar Matrícula y Pensiones</title>

<style>
:root{
    --azul-principal: #0b3c5d;      /* Azul institucional */
    --azul-secundario: #1f6aa5;
    --dorado: #c9a227;
    --gris-fondo: #f4f6f9;
    --gris-borde: #dcdfe3;
    --texto-oscuro: #333;
    --blanco: #ffffff;
}

*{
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--gris-fondo);
    color: var(--texto-oscuro);
}

/* Contenedor principal */
.container {
    max-width: 760px;
    margin: 50px auto;
    background: var(--blanco);
    padding: 30px 35px;
    border-radius: 14px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border-top: 6px solid var(--azul-principal);
}

/* Título */
h1 {
    margin-top: 0;
    margin-bottom: 10px;
    color: var(--azul-principal);
    font-size: 26px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Link volver */
.container a {
    display: inline-block;
    margin-bottom: 20px;
    color: var(--azul-secundario);
    text-decoration: none;
    font-weight: 600;
}

.container a:hover {
    text-decoration: underline;
}

/* Mensaje éxito */
.success {
    background: #e6f4ea;
    border-left: 5px solid #2e7d32;
    color: #2e7d32;
    padding: 12px 15px;
    border-radius: 6px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Formularios */
form label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: var(--azul-principal);
}

input[type="text"],
input[type="number"],
select {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid var(--gris-borde);
    font-size: 14px;
    transition: border 0.2s, box-shadow 0.2s;
}

input:focus,
select:focus {
    outline: none;
    border-color: var(--azul-secundario);
    box-shadow: 0 0 0 3px rgba(31,106,165,0.15);
}

/* Caja alumno */
#alumnoBox {
    margin-top: 10px;
}

/* Resultados búsqueda */
#resultadoAlumnos {
    border: 1px solid var(--gris-borde);
    border-radius: 8px;
    margin-top: -10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

#resultadoAlumnos div {
    transition: background 0.2s;
}

/* Botón principal */
button {
    width: 100%;
    padding: 14px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    background: linear-gradient(
        135deg,
        var(--azul-principal),
        var(--azul-secundario)
    );
    color: var(--blanco);
    box-shadow: 0 6px 15px rgba(11,60,93,0.3);
    transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(11,60,93,0.35);
    opacity: 0.95;
}

button:active {
    transform: translateY(0);
    box-shadow: 0 5px 12px rgba(11,60,93,0.25);
}

/* Responsive */
@media (max-width: 600px) {
    .container {
        margin: 20px;
        padding: 25px 20px;
    }

    h1 {
        font-size: 22px;
    }
}

/* Botón Volver al Inicio */
a.volver-inicio {
    display: inline-flex;                 
    align-items: center;
    justify-content: center;
    padding: 12px 22px;                   
    background: linear-gradient(135deg, var(--azul-secundario), var(--azul-principal));
    color: var(--blanco);
    font-weight: 600;
    font-size: 15px;
    border-radius: 10px;
    text-decoration: none;
    box-shadow: 0 6px 15px rgba(11,60,93,0.3);
    transition: all 0.3s ease;
    margin-bottom: 20px;                  
}

a.volver-inicio:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(11,60,93,0.35);
    opacity: 0.95;
}

a.volver-inicio:active {
    transform: translateY(0);
    box-shadow: 0 5px 12px rgba(11,60,93,0.25);
}

</style>
</head>

<body>
<div class="container">

<h1>💰 Modificar Montos</h1>
<a href="dashboard.php" class="volver-inicio">← Volver al Inicio</a>

<?php if (isset($_GET['msg'])): ?>
<p class="success">✅ Montos actualizados correctamente.</p>
<?php endif; ?>

<form method="post">

    <label>📅 Año Escolar</label>
    <input type="number" name="anio_year" value="<?=date('Y')?>" required>

    <label>💼 Tipo</label>
    <select name="tipo" required>
        <option value="matricula">Matrícula (Enero - Febrero)</option>
        <option value="pension">Pensiones (Marzo - Diciembre)</option>
    </select>

    <label>💵 Nuevo monto (S/)</label>
    <input type="number" step="0.01" name="monto" required>

    <label>👥 Aplicar a</label>
    <select name="alcance" id="alcance" required>
        <option value="alumno">Un estudiante</option>
        <option value="inicial">Todos Inicial</option>
        <option value="primaria">Todos Primaria</option>
        <option value="secundaria">Todos Secundaria</option>
    </select>

   <div id="alumnoBox" style="position:relative; max-width:400px;">
    <label>👩‍🎓 Alumno</label>
    <input type="text" id="buscarAlumno" placeholder="Escriba el apellido..."
           autocomplete="off" style="width:100%; padding:8px;">

    <input type="hidden" name="alumno_id" id="alumno_id">

    <div id="resultadoAlumnos"
         style="border:1px solid #ccc; background:#fff; position:absolute;
                width:100%; display:none; max-height:200px; overflow-y:auto;
                z-index:1000;">
    </div>
</div>

    <button type="submit" name="modificar_montos">💾 Modificar monto</button>

</form>
</div>

<script>
const alcance = document.getElementById('alcance');
const alumnoBox = document.getElementById('alumnoBox');


alcance.addEventListener('change', () => {
    alumnoBox.style.display = (alcance.value === 'alumno') ? 'block' : 'none';
});

const input = document.getElementById('buscarAlumno');
const resultados = document.getElementById('resultadoAlumnos');
const alumnoId = document.getElementById('alumno_id');

input.addEventListener('keyup', function () {
    const texto = this.value.trim();

    alumnoId.value = '';

    if (texto.length < 2) {
        resultados.style.display = 'none';
        resultados.innerHTML = '';
        return;
    }

    fetch('students_search.php?q=' + encodeURIComponent(texto))
        .then(res => res.json())
        .then(data => {

            resultados.innerHTML = '';

            if (data.length === 0) {
                resultados.innerHTML =
                    "<div style='padding:8px;color:#999;'>Sin resultados</div>";
                resultados.style.display = 'block';
                return;
            }

            data.forEach(a => {
                const div = document.createElement('div');
                div.style.padding = '8px';
                div.style.cursor = 'pointer';

                div.innerHTML = `
                    <b>${a.apellido_nombre}</b><br>
                    <small>DNI: ${a.dni} | Grado: ${a.grado ?? '-'}</small>
                `;

                div.onmouseover = () => div.style.background = '#f0f0f0';
                div.onmouseout = () => div.style.background = '#fff';

                div.onclick = () => {
                    input.value = a.apellido_nombre;
                    alumnoId.value = a.id;
                    resultados.style.display = 'none';
                };

                resultados.appendChild(div);
            });

            resultados.style.display = 'block';
        });
});
</script>

</body>
</html>
