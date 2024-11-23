<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include '../database.php';

// Obtener el ID del usuario desde la URL o el ID del usuario de sesión
$usuario_id = isset($_GET['userid']) ? intval($_GET['userid']) : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);

// Verificar que el ID sea válido
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
            $rol = htmlspecialchars($usuario['rol']);
            $avatar = !empty($usuario['avatar']) ? '../uploads/images/profile/' . $usuario['avatar'] : 'https://via.placeholder.com/100';
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
    if ($rol == 'estudiante') {
        // Consulta para obtener los cursos y materias que estudia el estudiante
        $cursos_query = "
        SELECT c.nombre AS curso_nombre, m.nombre AS materia_nombre
        FROM estudiante_curso ec
        INNER JOIN cursos c ON c.id = ec.curso_id
        INNER JOIN materias m ON m.id = c.materia_id
        WHERE ec.estudiante_id = ?";
    
        
        if ($cursos_stmt = $conn->prepare($cursos_query)) {
            $cursos_stmt->bind_param("i", $usuario_id); 
            $cursos_stmt->execute();
            $cursos_result = $cursos_stmt->get_result();
        }
    } elseif ($rol == 'profesor') {
        // Consulta para obtener los cursos y materias que enseña el profesor
        $materias_query = "
            SELECT c.nombre AS curso_nombre, m.nombre AS materia_nombre
            FROM cursos_dictados cd
            INNER JOIN cursos c ON c.id = cd.curso_id
            INNER JOIN materias m ON m.id = c.materia_id
            WHERE cd.profesor_id = ?";
        
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
    <!-- Agregar estilos de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Perfil de Usuario</h1>

        <div class="row">
            <!-- Avatar del usuario -->
            <div class="col-md-3">
                <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar de <?= $nombre ?>" class="img-fluid rounded-circle">
            </div>

            <div class="col-md-9">
                <h2>Información del Usuario</h2>
                <p><strong>Nombre:</strong> <?= $nombre ?></p>
                <p><strong>Email:</strong> <?= $email ?></p>
                <p><strong>Rol:</strong> <?= $rol ?></p>
            </div>
        </div>

        <div class="mt-5">
            <?php if ($rol == 'estudiante'): ?>
                <h2>Cursos y Materias que Estudia</h2>
                <?php if ($cursos_result->num_rows > 0): ?>
                    <ul>
                        <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                            <li><strong><?= htmlspecialchars($curso['curso_nombre']) ?>:</strong> <?= htmlspecialchars($curso['materia_nombre']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Este estudiante no está matriculado en ningún curso.</p>
                <?php endif; ?>
            <?php elseif ($rol == 'profesor'): ?>
                <h2>Cursos y Materias que Enseña</h2>
                <?php if ($materias_result->num_rows > 0): ?>
                    <ul>
                        <?php while ($materia = $materias_result->fetch_assoc()): ?>
                            <li><strong><?= htmlspecialchars($materia['curso_nombre']) ?>:</strong> <?= htmlspecialchars($materia['materia_nombre']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>Este profesor no enseña ningún curso.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <a href="index.php" class="btn btn-primary mt-3">Volver al inicio</a>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
