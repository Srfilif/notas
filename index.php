<?php
session_start();


 

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Notas</title>

    <!-- Enlazar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos personalizados */
        .hero {
            background-color: #f8f9fa;
            padding: 2rem;
            text-align: center;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .hero h1 {
            color: #007bff;
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #343a40;
        }

        .card-text {
            color: #6c757d;
        }

       
    </style>
</head>

<body>
    <!-- Nuevo header -->
    <?php include 'componentes/header.php'; ?>

    <main class="container my-4">
        <!-- Sección introductoria -->
        <div class="hero">
            <h1>Bienvenido al Sistema de Gestión de Notas</h1>
            <p>
                Aquí puedes gestionar, consultar y visualizar toda la información relacionada con las notas y 
                actividades académicas. Este sistema está diseñado para ofrecer una experiencia intuitiva y eficiente tanto para estudiantes como para profesores.
            </p>
        </div>

        <!-- Funcionalidades -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <i class="fa-solid fa-book fa-3x text-primary mb-2"></i>
                <h5>Gestión Académica</h5>
                <p>Consulta y administra tus cursos, notas y registros académicos.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-chart-line fa-3x text-success mb-2"></i>
                <h5>Estadísticas</h5>
                <p>Visualiza estadísticas detalladas de rendimiento académico.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-user-shield fa-3x text-warning mb-2"></i>
                <h5>Seguridad</h5>
                <p>Acceso controlado y datos protegidos para garantizar tu privacidad.</p>
            </div>
        </div>

        <!-- Panel de opciones basado en el rol -->
        <?php if (isset($_SESSION['usuario_id'])): ?>

        <?php if (($_SESSION['rol']) == 'estudiante'): ?>
            <h2 class="text-primary mb-4">Acceso Rápido</h2>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Notas</h5>
                            <p class="card-text">Consulta tus notas de las materias en las que estás inscrito.</p>
                            <a href="vernotas.php" class="btn btn-primary">Ver Notas</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Faltas</h5>
                            <p class="card-text">Consulta las faltas registradas para tus materias.</p>
                            <a href="verfaltas.php" class="btn btn-primary">Ver Faltas</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (($_SESSION['rol']) == 'profesor'): ?>
            <h2 class="text-primary mb-4">Acceso Rápido</h2>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Asignar Notas</h5>
                            <p class="card-text">Sube o edita las notas de los estudiantes en tus cursos.</p>
                            <a href="asignar_notas.php" class="btn btn-primary">Subir Notas</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ver Notas</h5>
                            <p class="card-text">Consulta las notas de todos los estudiantes de tus cursos.</p>
                            <a href="ver_notas_todos.php" class="btn btn-primary">Ver Notas</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Editar Notas</h5>
                            <p class="card-text">Modifica las notas de los estudiantes si es necesario (solo docentes).</p>
                            <a href="editar_notas.php" class="btn btn-primary">Editar Notas</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso no autorizado',
                    text: 'Tu rol no está definido correctamente. Contacta con el administrador.',
                    confirmButtonText: 'Cerrar'
                });
            </script>
        <?php endif; ?>
        <?php endif; ?>

    </main>

    <?php include 'componentes/footer.php'; ?>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
