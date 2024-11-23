<?php
session_start();

// Verificar que el usuario esté logueado y tenga el rol adecuado
if (!isset($_SESSION['usuario_id'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol']; // 'estudiante' o 'profesor'

// Determinar el mensaje para SweetAlert2
$mensaje_bienvenida = "Bienvenido a tu Dashboard, " . $_SESSION['usuario_nombre'] . "!"; // Suponiendo que el nombre está almacenado en sesión
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Enlazar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Nuevo header -->
   <?php include 'componentes/header.php';?>

    <main class="container my-4">
        <h1>Bienvenido al Dashboard</h1>

        <?php if ($rol == 'estudiante'): ?>
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

        <?php elseif ($rol == 'profesor'): ?>
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
                            <p class="card-text">Modifica las notas de los estudiantes si es necesario.</p>
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

    </main>

   <?php include 'componentes/footer.php';?>

    <script>
        // Mostrar alerta de bienvenida si el usuario está logueado
        <?php if (isset($_SESSION['usuario_id'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: '<?php echo $mensaje_bienvenida; ?>',
                confirmButtonText: 'Aceptar'
            });
        <?php endif; ?>
    </script>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script></body>

</html>
