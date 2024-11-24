<?php
// Iniciar sesión
session_start();

// Aquí deberías incluir la conexión a la base de datos si es necesario
// include 'database.php';

// Verificar si el usuario está autenticado (por ejemplo, si tiene una sesión activa)

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acerca de - Sistema de Notas</title>
    <!-- Agregar los enlaces de Bootstrap para el estilo -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Barra lateral y Topbar -->
    <?php include 'componentes/header.php'; ?>
    <main>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Sistema de Notas - Acerca de</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>¿Qué es el Sistema de Notas?</h4>
                <p>El sistema de notas es una plataforma diseñada para gestionar y evaluar el rendimiento académico de los estudiantes. Permite a los profesores registrar las calificaciones de los estudiantes y a los estudiantes consultar sus resultados de manera fácil y accesible.</p>
                <h4>Características principales:</h4>
                <ul>
                    <li>Registro de calificaciones por asignatura.</li>
                    <li>Visualización de notas finales y parciales.</li>
                    <li>Generación de informes académicos.</li>
                    <li>Acceso para estudiantes, profesores y administradores.</li>
                    <li>Soporte para múltiples asignaturas y semestres.</li>
                </ul>
            </div>

            <div class="col-md-6">
                <h4>Beneficios</h4>
                <p>El sistema de notas ofrece múltiples ventajas para mejorar la gestión académica:</p>
                <ul>
                    <li><strong>Accesibilidad:</strong> Los estudiantes pueden acceder a sus notas en cualquier momento y lugar.</li>
                    <li><strong>Facilidad de uso:</strong> Una interfaz simple y fácil de navegar.</li>
                    <li><strong>Seguridad:</strong> Los datos están protegidos y solo accesibles por usuarios autorizados.</li>
                    <li><strong>Integración:</strong> Puede integrarse con otros sistemas de gestión educativa.</li>
                </ul>
            </div>
        </div>

        <hr>

        <h3 class="text-center mt-4">¿Cómo funciona?</h3>
        <p class="text-center">El sistema permite a los profesores registrar las calificaciones de los estudiantes de manera rápida y sencilla, a través de formularios de fácil uso. Los estudiantes, por su parte, pueden ver sus calificaciones de forma inmediata, obteniendo una retroalimentación rápida.</p>

        <h4 class="text-center mt-4">Tecnologías utilizadas:</h4>
        <ul class="text-center">
            <li>PHP para la lógica del servidor.</li>
            <li>MySQL para la base de datos.</li>
            <li>HTML, CSS y JavaScript para la interfaz de usuario.</li>
            <li>Bootstrap para un diseño adaptable y limpio.</li>
        </ul>

        <hr>

        <h4 class="text-center mt-4">¿Quiénes somos?</h4>
        <p class="text-center">Somos un equipo de desarrolladores comprometidos con la mejora de los procesos educativos mediante el uso de la tecnología. Nuestro objetivo es proporcionar herramientas que optimicen la gestión de las calificaciones y el seguimiento académico.</p>
    </div>
    </main>
    <?php include 'componentes/footer.php'; ?>
    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
