<?php
include 'database.php';
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

// Obtener las materias del curso seleccionado
if ($curso_id) {
    $materias = $conn->query("SELECT * FROM materias WHERE curso_id = $curso_id");
}

// Procesar el formulario para crear una nueva categoría de nota
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
    <title>Agregar Categorías de Notas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Agregar Categorías de Notas</h1>

        <!-- Seleccionar Curso -->
        <form method="GET" action="">
            <label for="curso">Seleccionar Curso:</label>
            <select name="curso_id" id="curso" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <?php while ($curso = $cursos->fetch_assoc()): ?>
                    <option value="<?php echo $curso['id']; ?>" <?php echo $curso_id == $curso['id'] ? 'selected' : ''; ?>>
                        <?php echo $curso['nombre']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($curso_id): ?>
            <!-- Seleccionar Materia -->
            <form method="GET" action="">
                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                <label for="materia">Seleccionar Materia:</label>
                <select name="materia_id" id="materia" onchange="this.form.submit()">
                    <option value="">-- Seleccionar --</option>
                    <?php while ($materia = $materias->fetch_assoc()): ?>
                        <option value="<?php echo $materia['id']; ?>" <?php echo $materia_id == $materia['id'] ? 'selected' : ''; ?>>
                            <?php echo $materia['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        <?php endif; ?>

        <?php if ($materia_id): ?>
            <!-- Formulario para crear una nueva categoría de nota -->
            <form action="cnota.php?curso_id=<?php echo $curso_id; ?>&materia_id=<?php echo $materia_id; ?>" method="POST">
                <input type="hidden" name="materia_id" value="<?php echo $materia_id; ?>">

                <label for="nombre">Nombre de la Nota:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ejemplo: Nota 1" required>

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" placeholder="Ejemplo: Evaluación sobre X tema" required></textarea>

                <button type="submit" name="crear_categoria">Crear Categoría</button>
            </form>
        <?php elseif ($curso_id): ?>
            <p>Por favor, selecciona una materia para continuar.</p>
        <?php endif; ?>
    </div>
</body>
</html>
