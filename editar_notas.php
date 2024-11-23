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
            // Validar que la nota esté en el rango válido
            if ($nota >= 0 && $nota <= 5) {
                // Verificar si ya existe una nota para este estudiante y categoría
                $existe_nota = $conn->query("SELECT id FROM notas WHERE estudiante_id = $estudiante_id AND categoria_id = $categoria_id");

                if ($existe_nota->num_rows > 0) {
                    // Actualizar nota existente
                    $conn->query("UPDATE notas SET nota = $nota WHERE estudiante_id = $estudiante_id AND categoria_id = $categoria_id");
                } else {
                    // Insertar nueva nota
                    $conn->query("INSERT INTO notas (estudiante_id, categoria_id, materia_id, nota) VALUES ($estudiante_id, $categoria_id, $materia_id, $nota)");
                }
            }
        }
    }
    echo "<p>Notas guardadas correctamente.</p>";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Notas</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Asignar Notas</h1>

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
            <!-- Mostrar tabla de notas -->
            <br>
            <center>

            <?php if ($materia_id): ?>
                <form method="POST" action="">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <?php while ($categoria = $categorias_notas->fetch_assoc()): ?>
                                    <th><?php echo $categoria['nombre']; ?><br><small><?php echo $categoria['descripcion']; ?></small></th>
                                <?php endwhile; ?>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $numero_lista = 1; // Inicia el número de lista en 1
                            while ($estudiante = $estudiantes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $numero_lista++; ?></td> <!-- Muestra el número de lista y lo incrementa -->
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
                                            <input type="number" name="notas[<?php echo $estudiante['id']; ?>][<?php echo $categoria['id']; ?>]"
                                                value="<?php echo $nota_valor; ?>"
                                                min="0" max="5" step="0.1" />
                                        </td>
                                    <?php endwhile; ?>
                                    <td><?php echo $total_notas > 0 ? round($suma_notas / $total_notas, 2) : '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <br>

                    <button type="submit" name="guardar_notas">Guardar Notas</button>
                    <button><a href="./profesor.php">Volver</a></button>

                </form>
            <?php endif; ?>

            </center>

        <?php endif; ?>
    </div>

</body>

</html>
