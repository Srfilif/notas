<?php
include '../database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    die("Acceso denegado. Debes ser profesor para entrar.");
}

// Obtener todos los cursos
$cursos = $conn->query("SELECT * FROM cursos");

// Obtener curso y materia seleccionados
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : null;
$materia_id = isset($_GET['materia_id']) ? $_GET['materia_id'] : null;

$materias = [];
$estudiantes = [];
$categorias_notas = [];

// Obtener las materias del curso seleccionado
if ($curso_id) {
    $materias = $conn->query("SELECT * FROM materias WHERE curso_id = $curso_id");

    // Obtener estudiantes del curso seleccionado
    $estudiantes = $conn->query("SELECT * FROM estudiantes WHERE curso_id = $curso_id");
}

// Obtener las categorías de notas de la materia seleccionada
if ($materia_id) {
    $categorias_notas = $conn->query("SELECT * FROM categorias_notas WHERE materia_id = $materia_id");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_notas'])) {
    foreach ($_POST['notas'] as $estudiante_id => $categorias) {
        foreach ($categorias as $categoria_id => $nota) {
            if ($nota >= 0 && $nota <= 5) {
                $existe_nota = $conn->query("SELECT id FROM notas WHERE estudiante_id = $estudiante_id AND categoria_id = $categoria_id");
                if ($existe_nota->num_rows > 0) {
                    $conn->query("UPDATE notas SET nota = $nota WHERE estudiante_id = $estudiante_id AND categoria_id = $categoria_id");
                } else {
                    $conn->query("INSERT INTO notas (estudiante_id, categoria_id, materia_id, nota) VALUES ($estudiante_id, $categoria_id, $materia_id, $nota)");
                }
            }
        }
    }
    echo "<div class='alert alert-success'>Notas guardadas correctamente.</div>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    $materia_id = $_POST['materia_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    // Insertar la nueva categoría en la base de datos
    $query = $conn->prepare("INSERT INTO categorias_notas (materia_id, nombre, descripcion) VALUES (?, ?, ?)");
    $query->bind_param("iss", $materia_id, $nombre, $descripcion);

    if ($query->execute()) {
        echo "<script>
            alert('Categoría de nota creada exitosamente.');
            window.location.href = 'profesor.php?curso_id={$curso_id}&materia_id=$materia_id';
        </script>";
    } else {
        echo "<script>alert('Error al crear la categoría.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Notas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Asignar Notas</h1>

        <!-- Seleccionar Curso -->
        <form method="GET" action="" class="mb-3">
            <div class="mb-3">
                <label for="curso" class="form-label">Seleccionar Curso:</label>
                <select name="curso_id" id="curso" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Seleccionar --</option>
                    <?php while ($curso = $cursos->fetch_assoc()): ?>
                        <option value="<?php echo $curso['id']; ?>" <?php echo $curso_id == $curso['id'] ? 'selected' : ''; ?>>
                            <?php echo $curso['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <?php if ($curso_id): ?>
            <!-- Seleccionar Materia -->
            <form method="GET" action="" class="mb-3">
                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                <div class="mb-3">
                    <label for="materia" class="form-label">Seleccionar Materia:</label>
                    <select name="materia_id" id="materia" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleccionar --</option>
                        <?php while ($materia = $materias->fetch_assoc()): ?>
                            <option value="<?php echo $materia['id']; ?>" <?php echo $materia_id == $materia['id'] ? 'selected' : ''; ?>>
                                <?php echo $materia['nombre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($materia_id): ?>
            <div class="mb-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    Agregar Categoría de Nota
                </button>
            </div>

            <!-- Modal para agregar categoría -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCategoryModalLabel">Agregar Categoría de Nota</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="nueva_nota.php?curso_id=<?php echo $curso_id; ?>&materia_id=<?php echo $materia_id; ?>" method="POST">
                                <input type="hidden" name="materia_id" value="<?php echo $materia_id; ?>">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la Nota:</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ejemplo: Nota 1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción:</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Ejemplo: Evaluación sobre X tema" required></textarea>
                                </div>
                                <button type="submit" name="crear_categoria" class="btn btn-primary">Crear Categoría</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de notas -->
            <form method="POST" action="">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <?php while ($categoria = $categorias_notas->fetch_assoc()): ?>
                                    <th><?php echo $categoria['nombre']; ?><br><small><?php echo $categoria['descripcion']; ?></small></th>
                                <?php endwhile; ?>

                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $numero_lista = 1;
                            while ($estudiante = $estudiantes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $numero_lista++; ?></td>
                                    <td><?php echo $estudiante['id']; ?></td>
                                    <td><?php echo $estudiante['nombre']; ?></td>
                                    <?php
                                    $categorias_notas->data_seek(0);
                                    $suma_notas = 0;
                                    $total_notas = 0;
                                    while ($categoria = $categorias_notas->fetch_assoc()):
                                        $nota_query = $conn->query("SELECT nota FROM notas WHERE estudiante_id = {$estudiante['id']} AND categoria_id = {$categoria['id']}");
                                        $nota = $nota_query->fetch_assoc();
                                        $nota_valor = $nota ? $nota['nota'] : null;
                                        if ($nota_valor !== null) {
                                            $suma_notas += $nota_valor;
                                            $total_notas++;
                                        }
                                    ?>
                                        <td>
                                            <input type="number" class="form-control" name="notas[<?php echo $estudiante['id']; ?>][<?php echo $categoria['id']; ?>]" value="<?php echo $nota_valor; ?>" min="0" max="5" step="0.1">
                                        </td>
                                    <?php endwhile; ?>
                                   
                                    <td><?php echo $total_notas > 0 ? round($suma_notas / $total_notas, 2) : '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" name="guardar_notas" class="btn btn-success">Guardar Notas</button>
                <a href="./profesor.php" class="btn btn-secondary">Volver</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
