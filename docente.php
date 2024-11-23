<?php
include 'database.php';

// Obtener todos los cursos
$cursos = $conn->query("SELECT * FROM cursos");

// Obtener curso y materia seleccionados
$curso_id = isset($_GET['curso_id']) ? $_GET['curso_id'] : null;
$materia_id = isset($_GET['materia_id']) ? $_GET['materia_id'] : null;

$materias = [];
$estudiantes = [];

// Obtener las materias del curso seleccionado
if ($curso_id) {
    $materias = $conn->query("SELECT * FROM materias WHERE curso_id = $curso_id");
}

// Obtener los estudiantes del curso seleccionado
if ($curso_id) {
    $estudiantes = $conn->query("SELECT * FROM estudiantes WHERE curso_id = $curso_id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Notas - Profesor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Agregar Notas</h1>

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

        <?php if ($curso_id && $materia_id): ?>
            <!-- Formulario para agregar nota -->
            <form action="agregar_nota.php" method="POST">
                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                <input type="hidden" name="materia_id" value="<?php echo $materia_id; ?>">

                <label for="estudiante">Seleccionar Estudiante:</label>
                <select name="estudiante_id" id="estudiante" required>
                    <option value="">-- Seleccionar --</option>
                    <?php while ($estudiante = $estudiantes->fetch_assoc()): ?>
                        <option value="<?php echo $estudiante['id']; ?>">
                            <?php echo $estudiante['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="nota">Nueva Nota:</label>
                <input type="number" step="0.01" name="nota" id="nota" min="0" max="5" required>

                <button type="submit" name="agregar_nota">Agregar Nota</button>
            </form>
        <?php elseif ($curso_id): ?>
            <p>Por favor, selecciona una materia para continuar.</p>
        <?php endif; ?>
    </div>
</body>
</html>
