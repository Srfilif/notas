<?php
include '../database.php';
session_start();

// Verificación de sesión y permisos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: ../error.php?errorcode=403");
    die("Acceso denegado. Debes ser profesor para entrar.");
}

// Obtener todos los cursos
$cursos = $conn->query("SELECT * FROM cursos");

// Variables para manejar datos seleccionados
$curso_id = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
$materia_id = isset($_GET['materia_id']) ? (int)$_GET['materia_id'] : null;

$materias = [];
$categorias_notas = [];
$usuarios = [];  // Cambié "estudiantes" por "usuarios"

// Manejo de cursos y materias
if ($curso_id) {
    $materias = $conn->query("SELECT * FROM materias WHERE curso_id = $curso_id");

    // Aquí, cambiamos de "estudiantes" a "usuarios"
    $usuarios = $conn->query("SELECT * FROM cursos_actuales WHERE curso_id = $curso_id");
}

if ($materia_id) {
    $categorias_notas = $conn->query("SELECT * FROM categorias_notas WHERE materia_id = $materia_id");
}

// Crear nueva categoría de notas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    // Prevenir inyección SQL utilizando prepare y bind_param
    $query = $conn->prepare("INSERT INTO categorias_notas (materia_id, nombre, descripcion) VALUES (?, ?, ?)");
    $query->bind_param("iss", $materia_id, $nombre, $descripcion);

    if ($query->execute()) {
        echo "<script>
            alert('Categoría creada con éxito.');
            window.location.href = '?curso_id=$curso_id&materia_id=$materia_id';
        </script>";
    } else {
        echo "<script>alert('Error al crear la categoría.');</script>";
    }
}

// Editar categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_categoria'])) {
    $categoria_id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    // Prevenir inyección SQL utilizando prepare y bind_param
    $query = $conn->prepare("UPDATE categorias_notas SET nombre = ?, descripcion = ? WHERE id = ?");
    $query->bind_param("ssi", $nombre, $descripcion, $categoria_id);

    if ($query->execute()) {
        echo "<script>
            alert('Categoría actualizada con éxito.');
            window.location.href = '?curso_id=$curso_id&materia_id=$materia_id';
        </script>";
    } else {
        echo "<script>alert('Error al actualizar la categoría.');</script>";
    }
}

// Verificar si se ha solicitado eliminar una categoría
if (isset($_GET['eliminar_categoria_id'])) {
    $id_categoria = (int)$_GET['eliminar_categoria_id'];
    $materia_id = (int)$_GET['materia_id']; // Materia asociada

    // Ejecutar la eliminación de la categoría
    $conn->query("DELETE FROM categorias_notas WHERE id = $id_categoria");

    // Redirigir a la página de gestión de categorías después de la eliminación
    header("Location: instances.php?materia_id=$materia_id");
    exit;
}

// Copiar categoría
if (isset($_GET['copiar_categoria_id']) && isset($_GET['materia_id'])) {
    $id_categoria = (int)$_GET['copiar_categoria_id'];
    $materia_id = (int)$_GET['materia_id'];

    // Obtener la categoría que se quiere copiar
    $categoria = $conn->query("SELECT * FROM categorias_notas WHERE id = $id_categoria")->fetch_assoc();
    $nuevo_nombre = $categoria['nombre'];

    // Generar un nuevo nombre único para la categoría copiada
    $contador = 1;
    while ($conn->query("SELECT id FROM categorias_notas WHERE nombre = '$nuevo_nombre' AND materia_id = $materia_id")->num_rows > 0) {
        $contador++;
        $nuevo_nombre = $categoria['nombre'] . " ($contador)";
    }

    // Prevenir inyección SQL utilizando prepare y bind_param
    $query = $conn->prepare("INSERT INTO categorias_notas (materia_id, nombre, descripcion) VALUES (?, ?, ?)");
    $query->bind_param("iss", $materia_id, $nuevo_nombre, $categoria['descripcion']);
    $query->execute();

    // Redirigir a la página de gestión de categorías de la materia
    header("Location: instances.php?materia_id=$materia_id");
    exit;
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categoríass</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
            <h1 class="text-center text-primary">Gestión de instancias</h1>

            <!-- Selección de curso y materia -->
            <form method="GET" action="" class="mb-3">
                <label for="curso" class="form-label">Seleccionar Curso:</label>
                <select name="curso_id" id="curso" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Seleccionar --</option>
                    <?php while ($curso = $cursos->fetch_assoc()): ?>
                        <option value="<?php echo $curso['id']; ?>" <?php echo $curso_id == $curso['id'] ? 'selected' : ''; ?>>
                            <?php echo $curso['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <?php if ($curso_id): ?>
                    <label for="materia" class="form-label mt-3">Seleccionar Materia:</label>
                    <select name="materia_id" id="materia" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleccionar --</option>
                        <?php while ($materia = $materias->fetch_assoc()): ?>
                            <option value="<?php echo $materia['id']; ?>" <?php echo $materia_id == $materia['id'] ? 'selected' : ''; ?>>
                                <?php echo $materia['nombre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                <?php endif; ?>
            </form>

            <?php if ($materia_id): ?>
                <!-- Tabla de categorías -->
                <div class="mt-4">
                    <h2>Categorías Creadas</h2>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($categorias_notas->num_rows > 0): ?>
                                    <?php $num = 1; ?>
                                    <?php while ($categoria = $categorias_notas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $num++; ?></td>
                                            <td><?php echo $categoria['id']; ?></td>

                                            <td><?php echo $categoria['nombre']; ?></td>
                                            <td><?php echo $categoria['descripcion']; ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="editarCategoria(<?php echo $categoria['id']; ?>, '<?php echo $categoria['nombre']; ?>', '<?php echo $categoria['descripcion']; ?>')"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="confirmarEliminar('<?php echo $categoria['id']; ?>', '<?php echo $materia_id; ?>')">
                                                    <i class="fa-solid fa-trash"></i> Eliminar
                                                </a>

                                                <a href="javascript:void(0);"
                                                    class="btn btn-secondary btn-sm"
                                                    onclick="confirmarCopiar('<?php echo $categoria['id']; ?>', '<?php echo $materia_id; ?>')">
                                                    <i class="fa-solid fa-copy"></i> Copiar
                                                </a>

                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No hay categorías creadas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        Agregar Nueva Categoría
                    </button>

                    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCategoryModalLabel">Agregar Categoría</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción:</label>
                                            <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
                                        </div>
                                        <button type="submit" name="crear_categoria" class="btn btn-primary">Crear</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div>

                <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoría</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="editar_categoria" value="true">
                                    <input type="hidden" name="materia_id" value="<?php echo $materia_id; ?>">
                                    <input type="hidden" name="id" id="editCategoriaId">

                                    <div class="mb-3">
                                        <label for="editNombre" class="form-label">Nombre de la Categoría:</label>
                                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editDescripcion" class="form-label">Descripción:</label>
                                        <textarea name="descripcion" id="editDescripcion" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
        </div>
    </main>
    <?php include 'componentes/footer.php'; ?>

    <script>
        function editarCategoria(id, nombre, descripcion) {
            document.getElementById('editCategoriaId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editDescripcion').value = descripcion;
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }

        function confirmarEliminar(idCategoria, idMateria) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?eliminar_categoria_id=${idCategoria}&materia_id=${idMateria}`;
                }
            });
        }

        function confirmarCopiar(idCategoria, idMateria) {
            Swal.fire({
                title: '¿Quieres copiar esta categoría?',
                text: "Esto duplicará la categoría en la materia seleccionada.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, copiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?copiar_categoria_id=${idCategoria}&materia_id=${idMateria}`;
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>