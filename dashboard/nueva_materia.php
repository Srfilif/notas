<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: error.php");
    exit;
}

// Obtener la lista de cursos para el campo curso_id
$cursos = $conn->query("SELECT id, nombre FROM cursos");

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_materia = isset($_POST['nombre_materia']) ? trim($_POST['nombre_materia']) : '';
    $curso_id = isset($_POST['curso_id']) ? intval($_POST['curso_id']) : null;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

    if (!empty($nombre_materia) && $curso_id && !empty($descripcion) && $fecha_inicio && $fecha_fin) {
        $stmt = $conn->prepare("INSERT INTO materias (nombre, curso_id, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $nombre_materia, $curso_id, $descripcion, $fecha_inicio, $fecha_fin);
        if ($stmt->execute()) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Materia añadida correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Ups!',
                    text: 'La creacion de materia ha fallado',
                    icon: 'errror',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '¡Ups!',
                text: 'Tienes que rellenar todos los campos',
                icon: 'info',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Materia</title>
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
            background-color: #343a40;
            /* bg-dark */
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }


        main {
            margin-left: 280px;
            /* Ancho del sidebar */
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
        <h1 class="text-center text-primary">Nueva Materia</h1><br>


            <?php if (isset($mensaje)): ?>
                <div class="alert <?php echo strpos($mensaje, 'exitosamente') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombre_materia" class="form-label">Nombre de la Materia</label>
                    <input type="text" name="nombre_materia" id="nombre_materia" class="form-control" placeholder="Ingrese el nombre de la materia" required>
                </div>
                <div class="mb-3">
                    <label for="curso_id" class="form-label">Curso</label>
                    <select name="curso_id" id="curso_id" class="form-select" required>
                        <option value="">Seleccione un curso</option>
                        <?php while ($curso = $cursos->fetch_assoc()): ?>
                            <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4" placeholder="Ingrese una descripción para la materia" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Añadir Materia</button>
            </form>
        </div>
    </main>
    <?php include 'componentes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>