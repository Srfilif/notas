<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include '../database.php';

// Obtener el ID del usuario desde la URL o el ID del usuario de sesión
$usuario_id = isset($_GET['userid']) ? intval($_GET['userid']) : (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0);
// Inicializar las variables para los resultados de cursos
$cursos_result = null;
$materias_result = null;

// Verificar que el ID sea válidoA
if ($usuario_id > 0) {
    // Consulta para obtener la información del usuario
    $query = "SELECT id, nombre, email, fecha_registro, rol, avatar FROM usuarios WHERE id = ?";

    // Preparar la consulta
    if ($stmt = $conn->prepare($query)) {
        // Vincular el parámetro
        $stmt->bind_param("i", $usuario_id);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();

        // Verificar si se obtuvo el usuario
        if ($usuario = $result->fetch_assoc()) {
            // Asignar valores a las variables para mostrarlas en el perfil
            $nombre = htmlspecialchars($usuario['nombre']);
            $email = htmlspecialchars($usuario['email']);
            $perfil_rol = htmlspecialchars($usuario['rol']);
            $perfil_avatar = !empty($usuario['avatar']) ? '../uploads/images/profile/' . $usuario['avatar'] : 'https://via.placeholder.com/100';
        } else {
            echo "Usuario no encontrado.";
            exit;
        }

        // Cerrar la consulta preparada
        $stmt->close();
    } else {
        echo "Error en la consulta.";
        exit;
    }

    // Consultas para obtener cursos y materias según el rol
    if ($perfil_rol == 'estudiante') {
        // Consulta para obtener los cursos actuales del estudiante
        $cursos_query = "
        SELECT c.nombre AS curso_nombre, ca.fecha_inicio, ca.fecha_fin
        FROM cursos_actuales ca
        INNER JOIN cursos c ON c.id = ca.curso_id
        WHERE ca.usuario_id = ?";

        if ($cursos_stmt = $conn->prepare($cursos_query)) {
            $cursos_stmt->bind_param("i", $usuario_id);
            $cursos_stmt->execute();
            $cursos_result = $cursos_stmt->get_result();
        }
    } elseif ($perfil_rol == 'profesor') {
        // Consulta para obtener los cursos actuales que enseña el profesor
        $materias_query = "
        SELECT c.nombre AS curso_nombre, ca.fecha_inicio, ca.fecha_fin
        FROM cursos_actuales ca
        INNER JOIN cursos c ON c.id = ca.curso_id
        WHERE ca.usuario_id = ?";

        if ($materias_stmt = $conn->prepare($materias_query)) {
            $materias_stmt->bind_param("i", $usuario_id);
            $materias_stmt->execute();
            $materias_result = $materias_stmt->get_result();
        }
    }
} else {
    echo "ID de usuario no válido.";
    exit;
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <!-- Agregar estilos de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/styles.css">
</head>

<body>
    <aside>
        <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>

    <main>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Perfil de Usuario</h1>

            <div class="row justify-content-center">
                <!-- Avatar del usuario -->
                <div class="col-md-4 text-center">
                    <img src="<?= htmlspecialchars($perfil_avatar) ?>" alt="Avatar de <?= $nombre ?>" class="img-fluid rounded-circle" height="200px" width="200px">
                </div>

                <div class="col-md-8">
                    <h2 class="mb-3">Información del Usuario</h2>
                    <p><strong>Nombre:</strong> <?= $nombre ?></p>
                    <p><strong>Email:</strong> <?= $email ?></p>
                    <p><strong>Rol:</strong> <?= $perfil_rol ?></p>
                </div>
            </div>

            <div class="mt-5">
                <?php if ($perfil_rol == 'estudiante'): ?>
                    <h2>Cursos Actuales</h2>
                    <?php if ($cursos_result->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($curso['curso_nombre']) ?></h5>
                                            <p class="card-text">
                                                <?= $curso['fecha_inicio'] ? 'Inicio: ' . htmlspecialchars($curso['fecha_inicio']) : 'Fecha de inicio no disponible' ?>
                                                <br>
                                                <?= $curso['fecha_fin'] ? 'Fin: ' . htmlspecialchars($curso['fecha_fin']) : 'Fecha de fin no disponible' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>Este estudiante no está matriculado en ningún curso.</p>
                    <?php endif; ?>
                <?php elseif ($perfil_rol == 'profesor'): ?>
                    <h2>Cursos Actuales que Enseña</h2>
                    <?php if ($materias_result->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($materia = $materias_result->fetch_assoc()): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($materia['curso_nombre']) ?></h5>
                                            <p class="card-text">
                                                <?= $materia['fecha_inicio'] ? 'Inicio: ' . htmlspecialchars($materia['fecha_inicio']) : 'Fecha de inicio no disponible' ?>
                                                <br>
                                                <?= $materia['fecha_fin'] ? 'Fin: ' . htmlspecialchars($materia['fecha_fin']) : 'Fecha de fin no disponible' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>Este profesor no está asignado a ningún curso.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>
    </main>

    <?php include 'componentes/footer.php'; ?>
    <!-- Scripts de Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
