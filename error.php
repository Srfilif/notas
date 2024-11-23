<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <!-- Header -->
    <?php include 'componentes/header.php'; ?>

    <main class="error-page">

        <?php
        // Capturar el código de error desde la URL
        $errorcode = isset($_GET['errorcode']) ? $_GET['errorcode'] : 'unknown';

        // Configuración de mensajes y detalles según el código de error
        $error_messages = [
            '403' => [
                'title' => 'Error 403 - No Autorizado',
                'message' => 'No tienes autorización para acceder a esta página.',
                'advice' => 'Por favor, verifica tus permisos o inicia sesión con una cuenta válida.',
                'icon' => 'error',
                'button_text' => 'Ir a Login',
                'button_link' => 'login.php'
            ],
            '404' => [
                'title' => 'Error 404 - Página No Encontrada',
                'message' => 'La página que buscas no existe o ha sido movida.',
                'advice' => 'Por favor, verifica la URL o regresa al inicio.',
                'icon' => 'warning',
                'button_text' => 'Ir al Inicio',
                'button_link' => 'index.php'
            ],
            '500' => [
                'title' => 'Error 500 - Error Interno del Servidor',
                'message' => 'Ocurrió un problema en el servidor.',
                'advice' => 'Por favor, intenta nuevamente más tarde o contacta al administrador.',
                'icon' => 'error',
                'button_text' => 'Contactar Soporte',
                'button_link' => 'contacto.php'
            ],
            'unknown' => [
                'title' => 'Error Desconocido',
                'message' => 'Ocurrió un error inesperado.',
                'advice' => 'Por favor, regresa al inicio o contacta al administrador.',
                'icon' => 'info',
                'button_text' => 'Regresar',
                'button_link' => 'index.php'
            ],
            'soon' => [
                'title' => 'Contenido no Disponible',
                'message' => 'Este Contenido aun no esta disponible',
                'advice' => 'Prueba ingreando mas tarde, si crees que es un error, contacta con un administrador.',
                'icon' => 'error',
                'button_text' => 'Regresar',
                'button_link' => 'index.php'
            ],
        ];

        // Obtener los detalles del error según el código o usar el error desconocido
        $error_details = $error_messages[$errorcode] ?? $error_messages['unknown'];
        ?>

        <!-- Main content -->
        <div class="container text-center mt-5">
            <h1 class="display-4 text-danger"><?= $error_details['title'] ?></h1>
            <p class="lead"><?= $error_details['message'] ?></p>
            <p><?= $error_details['advice'] ?></p>
            <a href="<?= $error_details['button_link'] ?>" class="btn btn-primary btn-lg mt-3"><?= $error_details['button_text'] ?></a>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'componentes/footer.php'; ?>

    <!-- SweetAlert2 Alert -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= $error_details['icon'] ?>',
                title: '<?= $error_details['title'] ?>',
                text: '<?= $error_details['message'] ?>',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-mQ93WytbNaDJy9dOjfnwvFF0TehpkewD3qPgytE66V5qz5X5g5wJt/ZK+8dDfd6g" crossorigin="anonymous"></script>
</body>

</html>
