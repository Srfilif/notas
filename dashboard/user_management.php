<?php
session_start();
include '../database.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: error.php");
    exit;
}

// Obtener los estudiantes y los cursos asociados con sus fechas de inicio y fin
$estudiantes_query = $conn->query("
    SELECT usuarios.id AS usuario_id, usuarios.nombre, usuarios.email, usuarios.rol,
           cursos.nombre AS curso_nombre, cursos.id AS curso_id,
           cursos_actuales.fecha_inicio, cursos_actuales.fecha_fin
    FROM usuarios
    LEFT JOIN cursos_actuales ON usuarios.id = cursos_actuales.usuario_id
    LEFT JOIN cursos ON cursos_actuales.curso_id = cursos.id
");

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['añadirUsuario'])) {
    // Escapar entradas para prevenir inyecciones SQL
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $rol = htmlspecialchars(trim($_POST['rol']));
    $curso_id = !empty($_POST['curso_id']) ? intval($_POST['curso_id']) : null;

    // Validar campos requeridos
    if (!empty($nombre) && !empty($email) && !empty($rol)) {
        // Insertar el nuevo usuario en la base de datos
        $insert_query = $conn->prepare("INSERT INTO usuarios (nombre, email, rol) VALUES (?, ?, ?)");
        $insert_query->bind_param("sss", $nombre, $email, $rol);

        if ($insert_query->execute()) {
            // Si hay un curso asociado, enlazar al usuario con el curso
            if ($curso_id) {
                $usuario_id = $conn->insert_id; // Obtener el ID del usuario recién creado
                $link_query = $conn->prepare("INSERT INTO cursos_actuales (usuario_id, curso_id, fecha_inicio) VALUES (?, ?, NOW())");
                $link_query->bind_param("ii", $usuario_id, $curso_id);
                $link_query->execute();
            }

            // Mostrar mensaje de éxito
            $mensaje = "Usuario creado exitosamente.";
            $alerta = "success";
        } else {
            // Mostrar mensaje de error
            $mensaje = "Error al crear el usuario.";
            $alerta = "danger";
        }
    } else {
        // Mostrar mensaje de validación
        $mensaje = "Todos los campos requeridos deben ser completados.";
        $alerta = "warning";
    }
}

// Verificar si el formulario de edición fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $usuario_id = intval($_POST['usuario_id']);
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $curso_id = !empty($_POST['curso_id']) ? intval($_POST['curso_id']) : null;

    // Actualizar el usuario en la base de datos
    $update_query = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
    $update_query->bind_param("sssi", $nombre, $email, $rol, $usuario_id);

    if ($update_query->execute()) {
        // Actualizar curso asociado
        if ($curso_id) {
            // Verificar si ya existe un curso asociado
            $check_query = $conn->prepare("SELECT * FROM cursos_actuales WHERE usuario_id = ?");
            $check_query->bind_param("i", $usuario_id);
            $check_query->execute();
            $result = $check_query->get_result();

            if ($result->num_rows > 0) {
                // Actualizar curso existente
                $update_curso_query = $conn->prepare("UPDATE cursos_actuales SET curso_id = ?, fecha_inicio = NOW() WHERE usuario_id = ?");
                $update_curso_query->bind_param("ii", $curso_id, $usuario_id);
                $update_curso_query->execute();
            } else {
                // Insertar nuevo curso
                $insert_curso_query = $conn->prepare("INSERT INTO cursos_actuales (usuario_id, curso_id, fecha_inicio) VALUES (?, ?, NOW())");
                $insert_curso_query->bind_param("ii", $usuario_id, $curso_id);
                $insert_curso_query->execute();
            }
        }

        // Mostrar mensaje de éxito
        header("Location: " . $_SERVER['PHP_SELF']);

  
  
    } else {
        // Mostrar mensaje de error
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '!Ups!',
                text: 'Ha ocurrido un error inesperado',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario_id'])) {
    $usuario_id = intval($_POST['eliminar_usuario_id']);

    // Eliminar los cursos asociados
    $delete_cursos_query = $conn->prepare("DELETE FROM cursos_actuales WHERE usuario_id = ?");
    $delete_cursos_query->bind_param("i", $usuario_id);
    $delete_cursos_query->execute();

    // Eliminar el usuario
    $delete_usuario_query = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $delete_usuario_query->bind_param("i", $usuario_id);

    if ($delete_usuario_query->execute()) {
      
      
    header("Location: " . $_SERVER['PHP_SELF']);

    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '!Ups!',
                text: 'Ha ocurrido un error inesperado',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
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
            /* bg-dark */
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }


        main {
            margin-left: 280px;
            /* Ancho del sidebar */
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
            <h1 class="text-center mb-4">Gestionar Usuarios</h1>

            <div class="mb-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                    Nuevo Usuario
                </button>
            </div>


            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Curso Asociado</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $estudiantes_query->fetch_assoc()): ?>


                        <tr>
                            <td><?php echo $usuario['usuario_id']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                            <td><?php echo $usuario['curso_nombre'] ? htmlspecialchars($usuario['curso_nombre']) : 'No asociado'; ?></td>
                            <td><?php echo $usuario['fecha_inicio'] ? htmlspecialchars($usuario['fecha_inicio']) : 'No especificada'; ?></td>
                            <td><?php echo $usuario['fecha_fin'] ? htmlspecialchars($usuario['fecha_fin']) : 'No especificada'; ?></td>
                            <td>
                                <button
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarUsuarioModal"
                                    data-id="<?php echo $usuario['usuario_id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                                    data-email="<?php echo htmlspecialchars($usuario['email']); ?>"
                                    data-rol="<?php echo htmlspecialchars($usuario['rol']); ?>"
                                    data-curso-id="<?php echo $usuario['curso_id']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="eliminar_usuario_id" value="<?php echo $usuario['usuario_id']; ?>">
                                    <button type="button" class="btn btn-danger btn-sm eliminar-usuario" data-id="<?php echo $usuario['usuario_id']; ?>">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal para añadir un nuevo usuario -->
        <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="nuevoUsuarioModalLabel">Añadir Nuevo Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="estudiante">Estudiante</option>
                                    <option value="profesor">Profesor</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="curso_id" class="form-label">Curso Asociado (opcional)</label>
                                <select class="form-select" id="curso_id" name="curso_id">
                                    <option value="">Ninguno</option>
                                    <?php
                                    $cursos = $conn->query("SELECT id, nombre FROM cursos");
                                    while ($curso = $cursos->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $curso['id']; ?>">
                                            <?php echo htmlspecialchars($curso['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="añadirUsuario" class="btn btn-primary">Añadir Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="usuario_id" name="usuario_id">
                            <div class="mb-3">
                                <label for="editar_nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="editar_nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editar_email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="editar_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="editar_rol" class="form-label">Rol</label>
                                <select class="form-select" id="editar_rol" name="rol" required>
                                    <option value="estudiante">Estudiante</option>
                                    <option value="profesor">Profesor</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editar_curso_id" class="form-label">Curso Asociado (opcional)</label>
                                <select class="form-select" id="editar_curso_id" name="curso_id">
                                    <option value="">Ninguno</option>
                                    <?php
                                    $cursos = $conn->query("SELECT id, nombre FROM cursos");
                                    while ($curso = $cursos->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $curso['id']; ?>">
                                            <?php echo htmlspecialchars($curso['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" name="editar_usuario">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
    <?php include 'componentes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

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


<script>
    const editarUsuarioModal = document.getElementById('editarUsuarioModal');
    editarUsuarioModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Botón que activó el modal
        const usuarioId = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const email = button.getAttribute('data-email');
        const rol = button.getAttribute('data-rol');
        const cursoId = button.getAttribute('data-curso-id');

        // Asignar valores a los campos del formulario
        editarUsuarioModal.querySelector('#usuario_id').value = usuarioId;
        editarUsuarioModal.querySelector('#editar_nombre').value = nombre;
        editarUsuarioModal.querySelector('#editar_email').value = email;
        editarUsuarioModal.querySelector('#editar_rol').value = rol;
        editarUsuarioModal.querySelector('#editar_curso_id').value = cursoId;
    });
</script>
<script>
    document.querySelectorAll('.eliminar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const usuarioId = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'eliminar_usuario_id';
                    input.value = usuarioId;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>