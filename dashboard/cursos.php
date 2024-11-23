<?php
session_start();
include '../database.php';

// Verificar sesión y rol de usuario
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../error.php?errorcode=403");
    exit;
}

// Obtener cursos, materias y estudiantes
$cursos = $conn->query("
    SELECT 
        c.id AS curso_id, 
        c.nombre AS curso_nombre, 
        GROUP_CONCAT(DISTINCT m.nombre SEPARATOR ', ') AS materias, 
        GROUP_CONCAT(DISTINCT u.nombre SEPARATOR ', ') AS estudiantes
    FROM cursos c
    LEFT JOIN materias m ON c.id = m.curso_id
    LEFT JOIN cursos_actuales ca ON c.id = ca.curso_id
    LEFT JOIN usuarios u ON ca.usuario_id = u.id
    GROUP BY c.id, c.nombre
");

// Verificar si el parámetro 'editar' está presente en la URL
$materias_disponibles = $conn->query("SELECT id, nombre FROM materias WHERE curso_id = 22");
$materias_disponiblesx = $conn->query("SELECT id, nombre FROM materias WHERE curso_id = 22");



// Obtener usuarios
$usuariosx = $conn->query("
    SELECT usuarios.id, usuarios.nombre
    FROM usuarios
    LEFT JOIN cursos_actuales ON usuarios.id = cursos_actuales.usuario_id
    WHERE cursos_actuales.usuario_id IS NULL
");

$usuarios = $conn->query("
    SELECT usuarios.id, usuarios.nombre
    FROM usuarios
    LEFT JOIN cursos_actuales ON usuarios.id = cursos_actuales.usuario_id
    WHERE cursos_actuales.usuario_id IS NULL
");

if (isset($_GET['editar'])) {
    // Obtén el ID del curso
    $curso_id = intval($_GET['editar']);


    // Obtén el nombre del curso y las materias (si están en la URL)
    $curso_nombre = isset($_GET['curso_nombre']) ? $_GET['curso_nombre'] : '';
    $materias = isset($_GET['materias']) ? $_GET['materias'] : '';

    // Asegúrate de limpiar los valores con htmlspecialchars para evitar XSS
    $curso_nombre = htmlspecialchars($curso_nombre, ENT_QUOTES, 'UTF-8');
    $materias = htmlspecialchars($materias, ENT_QUOTES, 'UTF-8');
    $usuariosZ = $conn->query("
        SELECT DISTINCT usuarios.id, usuarios.nombre
        FROM usuarios
        INNER JOIN cursos_actuales ON usuarios.id = cursos_actuales.usuario_id
        WHERE cursos_actuales.curso_id = $curso_id
    ");
    $materias_actuales = $conn->query("SELECT id, nombre FROM materias WHERE curso_id = $curso_id");
    // Obtener materias disponibles con curso_id = 22
    if (!$cursos || !$usuarios || !$materias_actuales) {
        die("Error al obtener datos: " . $conn->error);
    }
    // Imprimir script para cargar los datos del curso en el modal
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            cargarDatosCurso($curso_id, '$curso_nombre', '$materias');
            var modal = new bootstrap.Modal(document.getElementById('modalEditarCurso'));
            modal.show();
        });
    </script>
    ";
}





// Editar curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_curso'])) {
    $curso_id = intval($_POST['curso_id']);
    $nuevo_nombre = $_POST['nombre_curso'];
    $materias = $_POST['materias'] ?? [];
    $usuarios_seleccionados = $_POST['usuarios'] ?? [];

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Actualizar nombre del curso
        $update_query = $conn->prepare("UPDATE cursos SET nombre = ? WHERE id = ?");
        $update_query->bind_param("si", $nuevo_nombre, $curso_id);
        $update_query->execute();

        // Actualizar materias: solo cambiar el curso_id

        // Corregir la consulta de actualización
        $conn->query("UPDATE materias SET curso_id = 22 WHERE curso_id = $curso_id");

        // Preparar la consulta de actualización segura
        $update_materia_query = $conn->prepare("UPDATE materias SET curso_id = ? WHERE id = ?");

        // Ejecutar la consulta para cada materia
        foreach ($materias as $materia_id) {
            $update_materia_query->bind_param("ii", $curso_id, $materia_id);
            $update_materia_query->execute();
        }


        // Actualizar estudiantes
        $conn->query("DELETE FROM cursos_actuales WHERE curso_id = $curso_id");
        $insert_usuario_query = $conn->prepare("INSERT INTO cursos_actuales (curso_id, usuario_id) VALUES (?, ?)");
        foreach ($usuarios_seleccionados as $usuario_id) {
            $insert_usuario_query->bind_param("ii", $curso_id, $usuario_id);
            $insert_usuario_query->execute();
        }

        // Confirmar transacción
        $conn->commit();
        header("Location: " . $_SERVER['PHP_SELF']);

        $mensaje = "Curso actualizado correctamente.";
        $alerta = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al actualizar el curso: " . $e->getMessage();
        $alerta = "danger";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_curso'])) {
    $nuevo_nombre = $_POST['nombre_nuevo_curso'];
    $materias = $_POST['materias'] ?? [];
    $usuarios_seleccionados = $_POST['usuarios'] ?? [];

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar el nuevo curso
        $insert_curso_query = $conn->prepare("INSERT INTO cursos (nombre) VALUES (?)");
        $insert_curso_query->bind_param("s", $nuevo_nombre);
        $insert_curso_query->execute();
        $nuevo_curso_id = $conn->insert_id;

        // Asociar materias al nuevo curso
        $update_materia_query = $conn->prepare("UPDATE materias SET curso_id = ? WHERE id = ?");
        foreach ($materias as $materia_id) {
            $update_materia_query->bind_param("ii", $nuevo_curso_id, $materia_id);
            $update_materia_query->execute();
        }

        // Asociar estudiantes al nuevo curso
        $insert_usuario_query = $conn->prepare("INSERT INTO cursos_actuales (curso_id, usuario_id) VALUES (?, ?)");
        foreach ($usuarios_seleccionados as $usuario_id) {
            $insert_usuario_query->bind_param("ii", $nuevo_curso_id, $usuario_id);
            $insert_usuario_query->execute();
        }

        // Confirmar transacción
        $conn->commit();
        $mensaje = "Curso creado correctamente.";
        $alerta = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje = "Error al crear el curso: " . $e->getMessage();
        $alerta = "danger";
    }
}


// Verificar si se ha recibido el parámetro delete
if (isset($_GET['delete'])) {
    // Obtener el ID del curso que se desea eliminar
    $cursoId = intval($_GET['delete']);

    // Iniciar una transacción para asegurar que todas las operaciones se realicen correctamente
    $conn->begin_transaction();

    try {
        // Paso 1: Obtener los IDs de las materias asociadas al curso
        $query_get_materias = "SELECT id FROM materias WHERE curso_id = ?";
        $stmt = $conn->prepare($query_get_materias);
        $stmt->bind_param("i", $cursoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $materiasIds = [];
        while ($row = $result->fetch_assoc()) {
            $materiasIds[] = $row['id'];
        }
        $stmt->close();

        // Paso 2: Eliminar registros de categorias_notas asociados a las materias
        if (!empty($materiasIds)) {
            $placeholders = implode(',', array_fill(0, count($materiasIds), '?'));
            $query_delete_categorias_notas = "DELETE FROM categorias_notas WHERE materia_id IN ($placeholders)";
            $stmt = $conn->prepare($query_delete_categorias_notas);
            $stmt->bind_param(str_repeat('i', count($materiasIds)), ...$materiasIds);
            $stmt->execute();
            $stmt->close();
        }

        // Paso 3: Eliminar las materias asociadas al curso
        $query_delete_materias = "DELETE FROM materias WHERE curso_id = ?";
        $stmt = $conn->prepare($query_delete_materias);
        $stmt->bind_param("i", $cursoId);
        $stmt->execute();
        $stmt->close();

        // Paso 4: Eliminar el curso
        $query_delete_curso = "DELETE FROM cursos WHERE id = ?";
        $stmt = $conn->prepare($query_delete_curso);
        $stmt->bind_param("i", $cursoId);
        $stmt->execute();
        $stmt->close();

        // Confirmar la transacción
        $conn->commit();

        // Redirigir con mensaje de éxito
        header("Location: cursos.php?message=Curso eliminado exitosamente");
        exit;
    } catch (Exception $e) {
        // Revertir la transacción si ocurre algún error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn->close();
}




?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cursos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            background-color: #343a40;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        main {
            margin-left: 280px;
            flex: 1;
            padding: 20px;
        }
    </style>
</head>

<body>
    <aside>
        <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>

    <main>
        <div class="container mt-5">
            <h1 class="text-center text-primary">Lista de Cursos</h1>

            <!-- Mensajes -->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $alerta; ?> mt-4" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de cursos -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Curso</th>
                            <th>Materias</th>
                            <th>Estudiantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($cursos->num_rows > 0): ?>
                            <?php while ($curso = $cursos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $curso['curso_id']; ?></td>
                                    <td><?php echo htmlspecialchars($curso['curso_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['materias'] ?? 'Sin materias asociadas'); ?></td>
                                    <td><?php echo htmlspecialchars($curso['estudiantes'] ?? 'Sin estudiantes inscritos'); ?></td>
                                    <td>
                                        <form action="cursos.php" method="get" class="d-inline">
                                            <!-- Campo oculto para enviar el ID del curso -->
                                            <input type="hidden" name="editar" value="<?php echo htmlspecialchars($curso['curso_id']); ?>">

                                            <!-- Campo oculto para enviar el nombre del curso -->
                                            <input type="hidden" name="curso_nombre" value="<?php echo htmlspecialchars($curso['curso_nombre'], ENT_QUOTES, 'UTF-8'); ?>">

                                            <!-- Campo oculto para enviar las materias -->
                                            <input type="hidden" name="materias" value="<?php echo htmlspecialchars($curso['materias'], ENT_QUOTES, 'UTF-8'); ?>">

                                            <!-- Botón de edición que activa el modal -->
                                            <button type="submit" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarCurso">
                                                <i class="fa-solid fa-pen-to-square"></i> Editar
                                            </button>
                                        </form>


                                        <a href="#"
                                            class="btn btn-danger btn-sm"
                                            onclick="confirmarEliminacion(<?php echo $curso['curso_id']; ?>);">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </a>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay cursos disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Botón para agregar un nuevo curso -->

        </div>
        <div class="mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoCurso">Crear Nuevo Curso</button>
        </div>

        <div class="modal fade" id="modalNuevoCurso" tabindex="-1" aria-labelledby="nuevoCursoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="nuevoCursoLabel">Nuevo Curso</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombreNuevoCurso" class="form-label">Nombre del Curso</label>
                                <input type="text" class="form-control" id="nombreNuevoCurso" name="nombre_nuevo_curso" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Materias Disponibles para añadir</label>
                                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                                    <!-- Aquí debes incluir la lógica PHP para mostrar las materias -->
                                    <?php while ($materia = $materias_disponibles->fetch_assoc()): ?>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="materia-<?php echo $materia['id']; ?>"
                                                name="materias[]"
                                                value="<?php echo $materia['id']; ?>">
                                            <label class="form-check-label" for="materia-<?php echo $materia['id']; ?>">
                                                <?php echo htmlspecialchars($materia['nombre']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Estudiantes</label>
                                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                                    <?php if ($usuarios->num_rows > 0): ?>
                                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="usuario-<?php echo $usuario['id']; ?>" name="usuarios[]" value="<?php echo $usuario['id']; ?>">
                                                <label class="form-check-label" for="usuario-<?php echo $usuario['id']; ?>">
                                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                                </label>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p>No hay estudiantes disponibles.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Agrega aquí el código para los estudiantes si es necesario -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="crear_curso" class="btn btn-primary">Crear Curso</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal para editar curso -->
        <div class="modal fade" id="modalEditarCurso" tabindex="-1" aria-labelledby="editarCursoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarCursoLabel">Editar Curso</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="curso_id" id="editarCursoId">
                            <div class="mb-3">
                                <label for="nombreCurso" class="form-label">Nombre del Curso</label>
                                <input type="text" class="form-control" id="nombreCurso" name="nombre_curso" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Materias Disponibles</label>
                                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                                    <?php while ($materia = $materias_actuales->fetch_assoc()): ?>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="materia-actual-<?php echo $materia['id']; ?>"
                                                name="materias[]"
                                                value="<?php echo $materia['id']; ?>"
                                                checked> <!-- Marcar las materias disponibles por defecto -->
                                            <label class="form-check-label" for="materia-actual-<?php echo $materia['id']; ?>">
                                                <?php echo htmlspecialchars($materia['nombre']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Materias Disponibles para añadir</label>
                                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                                    <!-- Aquí debes incluir la lógica PHP para mostrar las materias -->
                                    <?php while ($materia = $materias_disponiblesx->fetch_assoc()): ?>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="materia-<?php echo $materia['id']; ?>"
                                                name="materias[]"
                                                value="<?php echo $materia['id']; ?>">
                                            <label class="form-check-label" for="materia-<?php echo $materia['id']; ?>">
                                                <?php echo htmlspecialchars($materia['nombre']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Estudiantes</label>
                                <div class="border p-2 rounded" style="max-height: 200px; overflow-y: scroll;">
                                    <?php while ($usuario = $usuariosZ->fetch_assoc()): ?>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="usuario-<?php echo $usuario['id']; ?>"
                                                name="usuarios[]"
                                                value="<?php echo $usuario['id']; ?>">
                                            <label class="form-check-label" for="usuario-<?php echo $usuario['id']; ?>">
                                                <?php echo htmlspecialchars($usuario['nombre']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                    <?php if ($usuarios->num_rows > 0): ?>
                                        <?php while ($usuario = $usuariosx->fetch_assoc()): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="usuario-<?php echo $usuario['id']; ?>" name="usuarios[]" value="<?php echo $usuario['id']; ?>">
                                                <label class="form-check-label" for="usuario-<?php echo $usuario['id']; ?>">
                                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                                </label>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p>No hay estudiantes disponibles.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="editar_curso" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        </div>




    </main>


    <?php include 'componentes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    function cargarDatosCurso(id, nombre, materias) {
        // Cargar datos básicos
        document.getElementById('editarCursoId').value = id;

        document.getElementById('nombreCurso').value = nombre;

        // Limpiar todos los checkboxes



    }
</script>


<script>
    function confirmarEliminacion(cursoId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el curso y no podrá deshacerse.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a la URL de eliminación
                window.location.href = "?delete=" + cursoId;
            }
        });
    }
</script>