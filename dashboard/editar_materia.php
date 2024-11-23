<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: error.php");
    exit;
}

// Verificar si el ID de la materia fue proporcionado
if (!isset($_GET['id'])) {
    header("Location: ver_materias.php");
    exit;
}

$materia_id = intval($_GET['id']);

// Obtener los datos de la materia a editar
$materia_query = $conn->query("SELECT * FROM materias WHERE id = $materia_id");
$materia = $materia_query->fetch_assoc();

// Si no existe la materia, redirigir
if (!$materia) {
    header("Location: ver_materias.php");
    exit;
}

// Obtener la lista de cursos para el select
$cursos = $conn->query("SELECT id, nombre FROM cursos");

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $curso_id = intval($_POST['curso_id']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Actualizar los datos de la materia
    $update_query = "
        UPDATE materias
        SET nombre = '$nombre', curso_id = $curso_id, descripcion = '$descripcion', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin'
        WHERE id = $materia_id
    ";

    if ($conn->query($update_query)) {
        header("Location: ver_materias.php?mensaje=Materia actualizada correctamente");
        exit;
    } else {
        $error = "Error al actualizar la materia: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        aside {
            position: fixed;
            height: 100vh;
            width: 280px;
            background-color: #343a40; /* bg-dark */
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        
        main {
            margin-left: 280px; /* Ancho del sidebar */
            flex: 1;
            padding: 20px;
        }
    </style>
</head>

<body>
    <aside>
        <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>
    <main>


        <div class="container mt-5">
            <h1 class="text-center mb-4">Editar Materia</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Materia</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($materia['nombre']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="curso_id" class="form-label">Curso</label>
                    <select class="form-select" id="curso_id" name="curso_id" required>
                        <?php while ($curso = $cursos->fetch_assoc()): ?>
                            <option value="<?php echo $curso['id']; ?>" <?php echo $materia['curso_id'] == $curso['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($curso['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($materia['descripcion']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $materia['fecha_inicio']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $materia['fecha_fin']; ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="ver_materias.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>
    <?php include 'componentes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>