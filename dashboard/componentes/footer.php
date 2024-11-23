<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Componente - Sidebar</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/198d3df6d4.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Componente de pie de página -->
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        
        <div class="col-md-4 d-flex align-items-center">
            <!-- Logo -->
            <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                <img src="https://via.placeholder.com/40" alt="Logo" width="40" height="40">
            </a>
            <!-- Texto del pie de página -->
            <span class="mb-3 mb-md-0 text-muted">
                &copy; <?= date("Y"); ?> Sistema de notas para Instituciones
            </span>
        </div>

        <!-- Sección derecha: enlaces a redes sociales -->
        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
            <li class="ms-3">
                <a class="text-muted" href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </li>
            <li class="ms-3">
                <a class="text-muted" href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-facebook"></i>
                </a>
            </li>
            <li class="ms-3">
                <a class="text-muted" href="https://tiktok.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
            </li>
        </ul>
    </footer>

</body>
</html>
