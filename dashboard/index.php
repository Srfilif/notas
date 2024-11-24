<?php
session_start();

// Verificar que el usuario esté logueado y tenga el rol adecuado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../error.php?errorcode=403");
    die("Acceso denegado. Debes iniciar sesión.");
   

}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol']; // 'estudiante' o 'profesor'

include '../database.php'; // Asegúrate de que contiene la conexión $conn con MySQLi

try {
    // Consulta total de cursos
    $stmt_cursos = $conn->prepare("SELECT COUNT(*) AS total_cursos FROM cursos");
    $stmt_cursos->execute();
    $result_cursos = $stmt_cursos->get_result();
    $row_cursos = $result_cursos->fetch_assoc();
    $total_cursos = $row_cursos['total_cursos'];
    $stmt_cursos->close();

    // Consulta total de usuarios
    $stmt_usuarios = $conn->prepare("SELECT COUNT(*) AS total_usuarios FROM usuarios");
    $stmt_usuarios->execute();
    $result_usuarios = $stmt_usuarios->get_result();
    $row_usuarios = $result_usuarios->fetch_assoc();
    $total_usuarios = $row_usuarios['total_usuarios'];
    $stmt_usuarios->close();

    // Consulta total de materias
    $stmt_materias = $conn->prepare("SELECT COUNT(*) AS total_materias FROM materias");
    $stmt_materias->execute();
    $result_materias = $stmt_materias->get_result();
    $row_materias = $result_materias->fetch_assoc();
    $total_materias = $row_materias['total_materias'];
    $stmt_materias->close();

} catch (Exception $e) {
    echo "Error al consultar los totales: " . $e->getMessage();
    // Valores predeterminados en caso de error
    $total_cursos = 0;
    $total_usuarios = 0;
    $total_materias = 0;
}




?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Enlazar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        aside {
            position: fixed;
            height: 100vh;
            width: 280px;
            background-color: #343a40; /* bg-dark */
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        
        main {
            margin-left: 280px; /* Ancho del sidebar */
            flex: 1;
            padding: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside>
        <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>

    <!-- Contenido principal -->
    <main>
    <h1>Bienvenido al Dashboard</h1>

    <?php if ($rol == 'estudiante') : ?>
        <p>Hola, <?php echo ''.$_SESSION['usuario_nombre'].'' ?>. Este es tu panel como estudiante. Aquí podrás acceder a todas las herramientas necesarias para gestionar tu vida académica. Explora las opciones disponibles para consultar tus notas y faltas de asistencia.</p>
        
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

    <?php elseif ($rol == 'profesor') : ?>
        <p>Hola, <?php echo ''.$_SESSION['usuario_nombre'].'' ?>. Este es tu panel como profesor. Aquí podrás gestionar las notas y la información de los estudiantes en tus cursos. Utiliza estas herramientas para mantener todo organizado y actualizado.</p>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Estudiantes Registrados</h5>
                        <p class="card-text"><?php  echo $total_usuarios ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Materias Actuales</h5>
                        <p class="card-text"><?php  echo $total_materias ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Cursos</h5>
                        <p class="card-text"><?php echo $total_cursos; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Profesores</h5>
                        <p class="card-text">Unknow</p>
                    </div>
                </div>
            </div>
        </div>
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

    <?php endif; ?>
</main>


    <!-- Footer -->
    <?php include 'componentes/footer.php'; ?>

 

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
