<?php
$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Componente - Sidebar</title>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../public/css/styles.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/198d3df6d4.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
        <!-- Título del sistema -->
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="fa-solid fa-graduation-cap me-2"></i>
            <span class="fs-4">Sistema de Notas</span>
        </a>
        <hr>

        <!-- Menú de navegación -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" aria-current="page">
                    <i class="fa-solid fa-house"></i> Inicio
                </a>
            </li>

            <?php if ($rol === 'estudiante') : ?>
                <li>
                    <a href="seguimiento.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'seguimiento.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-solid fa-gauge"></i> Estado académico
                    </a>
                </li>
                <li>
                    <a href="../error.php?errorcode=soon" class="nav-link text-white">
                        <i class="fa-solid fa-message"></i> Faltas y Anotaciones
                    </a>
                </li>
            <?php elseif ($rol === 'profesor') : ?>
                <li>
                    <a href="instances.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'instances.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-regular fa-calendar-check"></i> Gestión de Instancias
                    </a>
                </li>
                <li>
                    <a href="academico.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'academico.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-solid fa-users"></i> Gestión de Notas
                    </a>
                </li>
            <?php elseif ($rol === 'desarrollador') : ?>
                <li>
                    <a href="nuevo_curso.php" class="nav-link text-white">
                        <i class="fa-regular fa-calendar-check"></i> Nuevo Curso
                    </a>
                </li>
                <li>
                    <a href="ver_cursos.php" class="nav-link text-white">
                        <i class="fa-solid fa-users"></i> Ver Cursos
                    </a>
                </li>
            <?php elseif ($rol === 'administrador') : ?>
                <li>
                    <a href="materias.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'materias.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-regular fa-calendar-check"></i> Gestión de Materias
                    </a>
                </li>
                <li>
                    <a href="user_management.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'user_management.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-solid fa-users"></i> Gestión de Usuarios
                    </a>
                </li>
                <li>
                    <a href="cursos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active' : ''; ?> text-white">
                        <i class="fa-solid fa-users"></i> Gestión de Cursos
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <hr>

        <!-- Perfil de usuario -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                include '../database.php';
                $avatar = 'https://via.placeholder.com/32'; // Valor predeterminado
                if ($_SESSION['usuario_id']) {
                    $avatar_query = $conn->query("SELECT avatar FROM usuarios WHERE id = $usuario_id");
                    $avatar_data = $avatar_query->fetch_assoc();
                    if (!empty($avatar_data['avatar'])) {
                        $avatar = '../uploads/images/profile/' . htmlspecialchars($avatar_data['avatar']);
                    }
                }
                ?>
                <img src="<?= $avatar ?>" alt="Usuario" width="32" height="32" class="rounded-circle me-2">
                <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></strong>
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