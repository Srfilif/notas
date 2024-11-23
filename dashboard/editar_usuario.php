<?php
session_start();
include '../database.php';

// Verificar si el usuario es administrador


// Obtener el ID del usuario a editar
if (isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    // Obtener los datos del usuario
    $usuario_query = $conn->query("
        SELECT usuarios.id, usuarios.nombre, usuarios.email, usuarios.rol,
             usuarios.id AS estudiante_id, cursos_actuales.curso_id, cursos_actuales.fecha_inicio, cursos_actuales.fecha_fin
        FROM usuarios
        LEFT JOIN cursos_actuales ON usuarios.id = cursos_actuales.usuario_id
        WHERE usuarios.id = $usuario_id
    ");




    $usuario = $usuario_query->fetch_assoc();
    if (!$usuario) {
        // Si el usuario no existe, redirigir al administrador
        header("Location: gestion_usuarios.php");
        exit;
    }
} else {
    // Si no se proporciona el ID del usuario, redirigir al administrador
    header("Location: gestion_usuarios.php");
    exit;
}

// Guardar los cambios del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $curso_id = $_POST['curso_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Actualizar los datos del usuario
    $update_usuario_query = "
        UPDATE usuarios
        SET nombre = '$nombre', email = '$email', rol = '$rol'
        WHERE id = $usuario_id
    ";

    // Ejecutar la actualización
    $conn->query($update_usuario_query);

    // Si hay un curso asociado, actualizar las fechas
    if ($curso_id && $usuario['estudiante_id']) {
        $update_curso_query = "
            UPDATE cursos_actuales
            SET curso_id = '$curso_id', fecha_inicio = '$fecha_inicio', fecha_fin = '$fecha_fin'
            WHERE usuario_id = $usuario_id
        ";
        $conn->query($update_curso_query);
    }

    // Redirigir a la página de gestión de usuarios
    header("Location: gestion_usuarios.php");
    exit;
}

// Obtener todos los cursos disponibles para asignar
$cursos_query = $conn->query("SELECT * FROM cursos");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
            <h1 class="text-center mb-4">Editar Usuario</h1>

            <form method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select" id="rol" name="rol">
                        <option value="administrador" <?php echo ($usuario['rol'] === 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="estudiante" <?php echo ($usuario['rol'] === 'estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                        <option value="profesor" <?php echo ($usuario['rol'] === 'profesor') ? 'selected' : ''; ?>>Profesor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="curso_id" class="form-label">Curso Asociado</label>
                    <select class="form-select" id="curso_id" name="curso_id">
                        <option value="">Seleccionar Curso</option>
                        <?php while ($curso = $cursos_query->fetch_assoc()): ?>
                            <option value="<?php echo $curso['id']; ?>" <?php echo ($curso['id'] == $usuario['curso_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($curso['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $usuario['fecha_inicio'] ? $usuario['fecha_inicio'] : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $usuario['fecha_fin'] ? $usuario['fecha_fin'] : ''; ?>">
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a href="gestion_usuarios.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>