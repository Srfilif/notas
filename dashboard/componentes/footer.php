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
    <style>
        footer{
            padding-left: 300px;
            padding-right: 20px;
            color: white;
        }
    </style>
</head>

<body>
  

    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <div class="col-md-4 d-flex align-items-center">
            <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                <img src="https://via.placeholder.com/30" alt="Logo" width="30" height="24">
            </a>
            <span class="mb-3 mb-md-0 text-muted">&copy; <?= date("Y"); ?> Instituto Desarrollo Económico del Sur</span>
        </div>

        <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
            <li class="ms-3">
                <a class="text-muted" href="https://twitter.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </li>
            <li class="ms-3">
                <a class="text-muted" href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-facebook"></i>
                </a>
            </li>
            <li class="ms-3">
                <a class="text-muted" href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
            </li>
        </ul>
    </footer>
</body>

</html>
