<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y tiene el rol adecuado (por ejemplo, profesor o administrador)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../error.php?errorcode=403");
    exit;
}

// Obtener la lista de materias que no tienen un curso asociado
$materias = $conn->query("
    SELECT id, nombre 
    FROM materias 
    WHERE id NOT IN (SELECT materia_id FROM cursos)
");

// Procesar el formulario de creación de curso
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $materia_id = intval($_POST['materia_id']);

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && $materia_id > 0) {
        $query = "INSERT INTO cursos (nombre, materia_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nombre, $materia_id);

        if ($stmt->execute()) {
            $mensaje = "Curso creado exitosamente.";
            $alerta = "success";
        } else {
            $mensaje = "Error al crear el curso. Intenta nuevamente.";
            $alerta = "danger";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
        $alerta = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Curso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        main {
            margin-left: 280px;
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
            <h1 class="text-center text-primary">Crear Nuevo Curso</h1>

            <!-- Mostrar mensajes -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $alerta; ?> mt-4" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" action="" class="mt-4">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Curso</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre del curso" required>
                </div>
                <div class="mb-3">
                    <label for="materia_id" class="form-label">Materia</label>
                    <select class="form-select" id="materia_id" name="materia_id" required>
                        <option value="">Seleccionar una materia</option>
                        <?php while ($materia = $materias->fetch_assoc()): ?>
                            <option value="<?php echo $materia['id']; ?>">
                                <?php echo htmlspecialchars($materia['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Crear Curso</button>
            </form>

            <div class="mt-5">
                <a href="ver_cursos.php" class="btn btn-secondary">Ver Cursos</a>
            </div>
        </div>
    </main>

    <?php include 'componentes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
