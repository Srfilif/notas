<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Componet - Header</title>

    <!-- Enlazar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>


<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
            <img src="https://via.placeholder.com/60" alt="Logo" width="60" height="44">
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="./index.php" class="nav-link px-2 <?= $current_page === 'index.php' ? 'text-secondary' : 'text-white' ?>">Inicio</a></li>
                <li><a href="./dash.php" class="nav-link px-2 <?= $current_page === 'dash.php' ? 'text-secondary' : 'text-white' ?>">Dashboard</a></li>
                <li><a href="./vernotas.php" class="nav-link px-2 <?= $current_page === 'header.php' ? 'text-secondary' : 'text-white' ?>">Notas</a></li>
                <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
                <li><a href="#" class="nav-link px-2 text-white">About</a></li>
            </ul>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search">
            </form>

            <div class="text-end">
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <button type="button" class="btn btn-outline-light me-2" onclick="window.location.href='login.php'">Ingresar</button>
                    <button type="button" class="btn btn-warning" onclick="window.location.href='signup.php'">Registro</button>
                <?php else: ?>
                    <a href="logout.php" class="btn btn-outline-light">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>