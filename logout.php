<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Mostrar un mensaje con SweetAlert2
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>No estás logueado</title>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "No estás logueado",
                    text: "Parece que no estas logeado, Por lo tanto no podras cerrar seccion.",
                    showCancelButton: true,
                    confirmButtonText: "Ir al login",
                    cancelButtonText: "Regresar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "login.php";
                    } else {
                        window.history.back();
                    }
                });
            });
        </script>
    </body>
    </html>';
    // Detener la ejecución del resto del script
    exit;
}

// Destruir todas las variables de sesión
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">

    <style>

    </style>
</head>
<?php include 'componentes/header.php'; ?>

<body>

    <center style="padding-top: 5%;">

        <div class="logout-container">
            <img src="https://via.placeholder.com/100" alt="Logo">
            <h1>Has cerrado sesión </h1>
            <p>Gracias por utilizar nuestra plataforma.</p>
            <a href="login.php" class="btn">ir a Login</a>
            <footer>
                <p>&copy; 2024 Filif Company Inc | Developed by <a href="https://srfilif.github.io">SrFilif</a></p>
            </footer>
        </div>
    </center>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'componentes/footer.php'; ?>

</html>