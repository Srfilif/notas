<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y tiene rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: error.php?errorcode=403");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_materia'])) {
    $nombre = $_POST['cnombre'];
    $descripcion = $_POST['cdescripcion'];
    $curso_id = intval($_POST['ccurso_id']);
    $fecha_inicio = $_POST['cfecha_inicio'];
    $fecha_fin = $_POST['cfecha_fin'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO materias (nombre, descripcion, curso_id, fecha_inicio, fecha_fin)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiss', $nombre, $descripcion, $curso_id, $fecha_inicio, $fecha_fin);

        if ($stmt->execute()) {
            // Mostrar mensaje de éxito
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'La materia se ha creado correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    });
                });
            </script>";
        } else {
            // Capturar el error de ejecución
            $error = $stmt->error;
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: '¡Ups!',
                        text: 'Error al intentar crear la materia: $error',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                });
            </script>";
        }

        $stmt->close();
    } catch (Exception $e) {
        // Capturar errores excepcionales
        $error = $e->getMessage();
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Se produjo un error inesperado: $error',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notas WHERE materia_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Se produjo un error inesperado: ',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";
        } else {
            $stmt = $conn->prepare("DELETE FROM materias WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            
        }
    } catch (Exception $e) {
 echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Se produjo un error inesperado: ',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>";    }
}



// Procesar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
    $id = intval($_POST['editar_id']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $curso_id = intval($_POST['curso_id']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $stmt = $conn->prepare("
        UPDATE materias 
        SET nombre = ?, descripcion = ?, curso_id = ?, fecha_inicio = ?, fecha_fin = ?
        WHERE id = ?
    ");
    $stmt->bind_param('ssisii', $nombre, $descripcion, $curso_id, $fecha_inicio, $fecha_fin, $id);
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
        c.id AS curso_id,
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
                                <button class="btn btn-sm btn-warning" onclick="abrirModalEditar(
        <?php echo $materia['id']; ?>, 
                                        '<?php echo addslashes($materia['nombre']); ?>',
                                        '<?php echo addslashes($materia['descripcion']); ?>',
                                        <?php echo $materia['curso_id']; ?>,
                                        '<?php echo $materia['fecha_inicio']; ?>',
                                        '<?php echo $materia['fecha_fin']; ?>'
                                    )">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </button>

                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmarEliminar('<?php echo $materia['id']; ?>')">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="mb-4 text-start">
                <!-- Botón para abrir el modal de creación -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">Crear Nueva
                    Materia</button>
            </div>
        </div>

    </main>

    <!-- Modal para Crear -->
    <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCrearLabel">Crear Nueva Materia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="crear_materia" value="1">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="cnombre" id="cnombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="cdescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="cdescripcion" id="cdescripcion" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="ccurso_id" class="form-label">Curso</label>
                            <select class="form-control" name="ccurso_id" id="ccurso_id" required>
                                <?php
                                $cursos = $conn->query("SELECT id, nombre FROM cursos");
                                while ($curso = $cursos->fetch_assoc()): ?>
                                    <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nombre']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="cfecha_inicio" id="cfecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" name="cfecha_fin" id="cfecha_fin" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Materia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal para Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarLabel">Editar Materia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="editar_id" id="editar_id">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="curso_id" class="form-label">Curso</label>
                            <select class="form-control" name="curso_id" id="curso_id" required>
                                <?php
                                $cursos = $conn->query("SELECT id, nombre FROM cursos");
                                while ($curso = $cursos->fetch_assoc()): ?>
                                    <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nombre']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function abrirModalEditar(id, nombre, descripcion, curso_id, fecha_inicio, fecha_fin) {
            // Asignar valores a los campos del modal
            console.log('ID:', id);
            console.log('Nombre:', nombre);
            console.log('Descripción:', descripcion);
            console.log('Curso ID:', curso_id);

            document.getElementById('editar_id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('descripcion').value = descripcion;
            document.getElementById('curso_id').value = curso_id;
            document.getElementById('fecha_inicio').value = fecha_inicio;
            document.getElementById('fecha_fin').value = fecha_fin;

            // Mostrar el modal
            const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
            modalEditar.show();
        }


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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>