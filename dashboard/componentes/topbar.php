<?php $usuario_id = $_SESSION['usuario_id']; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Componente - Topbar</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/198d3df6d4.js" crossorigin="anonymous"></script>

    <!-- Estilos personalizados -->
    <style>
        .nav-link.active {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .nav-link {
            color: #fff !important;
        }

        .navbarfl {

            padding-left: 280px;

        }
    </style>
</head>

<body>
    <!-- Topbar Navbar -->
    <div class="navbarfl">
        <nav class="navbar navbar-expand navbar-dark bg-dark">
            <ul class="navbar-nav ms-auto">
                <!-- Nav Item - Search Bar -->
                <li class="nav-item w-100">
                    <form class="d-flex mx-auto" style="width: 100%;">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </li>

                <!-- Nav Item - Alerts -->
                <li class="nav-item dropdown mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger rounded-pill">9+</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">Centro de alertas</h6>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <div class="ms-3">
                                <div class="small text-muted">18 Noviembre 2024</div>
                                Se esta trabajando en el sistema de alertas!
                            </div>
                        </a>
                    </div>
                </li>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2 d-none d-lg-inline text-light"><?= $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></span>
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

                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="../error.php?errorcode=soon"><i class="fas fa-cogs me-2"></i>Opciones</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Seccion</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-7wXycEm9hQl5YPhNMGxzzP8yRxFIvRYxtZG8lgLzzsl7KfRHyWr4EzV1O/uK9l66" crossorigin="anonymous"></script>
</body>

</html>