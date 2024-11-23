<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: error.php?errorcode=403");
    exit;
}

// Procesar la eliminación segura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM materias WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar reenvíos de formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Obtener las materias con un JOIN
$query = "
    SELECT 
        m.id, 
        m.nombre, 
        m.descripcion, 
        c.nombre AS curso, 
        m.fecha_inicio, 
        m.fecha_fin 
    FROM materias m
    JOIN cursos c ON m.curso_id = c.id
";
$materias = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Materias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <aside>
        <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>

    <main style="margin-left: 280px; padding: 20px; flex:1;">
        <div class="container mt-5">
            <h1 class="text-center text-primary">Lista de Materias</h1><br>
            <p>Acontinuacion veras una lista detallada con las materias actualmente creadas:</p>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Curso</th>
                        <th>Descripción</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($materia = $materias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $materia['id']; ?></td>
                            <td><?php echo $materia['nombre']; ?></td>
                            <td><?php echo $materia['curso']; ?></td>
                            <td><?php echo $materia['descripcion']; ?></td>
                            <td><?php echo $materia['fecha_inicio']; ?></td>
                            <td><?php echo $materia['fecha_fin']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminar('<?php echo $materia['id']; ?>')">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                                <a href="editar_materia.php?id=<?php echo $materia['id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="mt-4">
                <a href="nuevo_curso.php" class="btn btn-primary">Crear Nuevo Materia</a>
            </div>
        </div>
    </main>

    <script>
        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás deshacer esta acción.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario de eliminación usando POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'eliminar_id';
                    input.value = id;
                    form.appendChild(input);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>

</html>
