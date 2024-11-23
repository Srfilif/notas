<?php $usuario_id = $_SESSION['usuario_id']; 
$rol = $_SESSION['rol']
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Componente - Sidebar</title>

    <!-- Enlazar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/198d3df6d4.js" crossorigin="anonymous"></script>

    <!-- Personalización de la clase active -->
    <style>
        .nav-link.active {
            background-color: #0d6efd !important;
            /* Azul de Bootstrap */
            color: white !important;
            /* Cambia el color de la fuente a blanco */
        }

        /* Asegurarse de que los enlaces no activos tengan color blanco */
        .nav-link {
            color: #fff !important;
            /* Asegura que el texto sea blanco */
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <svg class="bi me-2" width="40" height="32">
                <use xlink:href="#bootstrap" />
            </svg>
            <span class="fs-4">Sistema de Notas</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" aria-current="page">
                    <i class="fa-solid fa-house"></i>
                    Inicio
                </a>
            </li>
            <?php if ($rol == 'estudiante') : ?>

        <hr>
            <li>
                <a href="seguimiento.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'seguimiento.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-gauge"></i>
                    Estado académico
                </a>
            </li>

            <li>
                <a href="../error.php?errorcode=soon" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notas.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-message"></i>
                    Faltas y Anotaciones
                </a>
            </li>
            
            <?php elseif ($rol == 'profesor') : ?>
                <hr>
            <li>
                <a href="instances.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'instances.php' ? 'active' : ''; ?> text-white" aria-current="page">
                    <i class="fa-regular fa-calendar-check"></i> Gestion de Instancias
                </a>
            </li>
            <li>
                <a href="academico.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'academico.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i>  Gestion de Notas
                </a>
            </li>
        
            <?php elseif ($rol == 'desarrollador') : ?>
            <hr>

            <li>
                <a href="nuevo_curso.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'nuevo_curso.php' ? 'active' : ''; ?> text-white" aria-current="page">
                    <i class="fa-regular fa-calendar-check"></i> Nuevo Curso
                </a>
            </li>
            <li>
                <a href="ver_cursos.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ver_cursos.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i> Ver Cursos
                </a>
            </li>
            <li>
                <a href="user_management.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i> Usuarios
                </a>
            </li>
            <?php elseif ($rol == 'administrador') : ?>
            <hr>
            <li>
                <a href="materias.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'materias.php' ? 'active' : ''; ?> text-white" aria-current="page">
                    <i class="fa-regular fa-calendar-check"></i> Gestionar Materias
                </a>
            </li>
            <li>
                <a href="nueva_materia.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'nueva_materia.php' ? 'active' : ''; ?> text-white" aria-current="page">
                    <i class="fa-regular fa-calendar-check"></i> Nueva Materia
                </a>
            </li>
     
            <li>
                <a href="cursos.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i> Lista de  Cursos
                </a>
            </li>
            <li>
                <a href="nuevo_curso.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'nuevo_curso.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i> Nuevo Curso
                </a>
            </li>
            <hr>
            <li>
                <a href="user_management.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : ''; ?> text-white">
                    <i class="fa-solid fa-users"></i> Gestion de Usuarios
                </a>
            </li>
         
            <?php endif; ?>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <?php

                include '../database.php';
                if ($_SESSION['usuario_id']) {
                    // Consulta para obtener el avatar del usuario
                    $avatar_query = $conn->query("SELECT avatar FROM usuarios WHERE id = $usuario_id");

                    // Obtener el resultado
                    $avatar_data = $avatar_query->fetch_assoc(); // Asume que estás usando MySQLi

                    // Establecer el avatar o un marcador de posición
                    $avatar = !empty($avatar_data['avatar']) ? '../uploads/images/profile/' . $avatar_data['avatar'] : 'https://via.placeholder.com/32';
                }
                ?>

                <img src="<?= htmlspecialchars($avatar) ?>" alt="Usuario" width="32" height="32" class="rounded-circle me-2">

                <strong><?= $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="configuracion.php">Configuración</a></li>
                <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="../logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</body>

</html>