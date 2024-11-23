<?php
session_start();
include '../database.php';

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: ../error.php?errorcode=403");
    exit;
}

// Obtener el ID de la materia seleccionada (si lo hay)
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : null;

// Obtener el ID del usuario
$usuario_id = $_SESSION['usuario_id'];

// Consultar los cursos actuales del estudiante
$cursos_query = "
    SELECT c.id, c.nombre 
    FROM cursos c
    JOIN cursos_actuales ca ON c.id = ca.curso_id
    WHERE ca.usuario_id = $usuario_id
";

$cursos = $conn->query($cursos_query);

// Consultar las materias del estudiante a través de sus cursos actuales
$materias_query = "
    SELECT DISTINCT m.id, m.nombre 
    FROM materias m
    JOIN cursos_actuales ca ON m.curso_id = ca.curso_id
    WHERE ca.usuario_id = $usuario_id
";

$materias = $conn->query($materias_query);

// Consultar las notas y categorías asociadas al estudiante
$notas_query = "
    SELECT 
        n.nota, 
        m.nombre AS materia, 
        c.nombre AS categoria, 
        m.id AS materia_id 
    FROM notas n
    JOIN materias m ON n.materia_id = m.id
    JOIN categorias_notas c ON n.categoria_id = c.id
    WHERE n.usuario_id = $usuario_id
";

$categorias_query = "
    SELECT 
        c.nombre AS categoria, 
        m.nombre AS materia, 
        m.id AS materia_id 
    FROM categorias_notas c
    JOIN materias m ON c.materia_id = m.id
";

// Filtrar por materia si se seleccionó
if ($materia_id) {
    $notas_query .= " AND m.id = $materia_id";
    $categorias_query .= " WHERE m.id = $materia_id";
}

$notas = $conn->query($notas_query);
$categorias = $conn->query($categorias_query);

// Agrupar notas y categorías por materia
$materias_notas = [];
while ($nota = $notas->fetch_assoc()) {
    $materias_notas[$nota['materia']][] = $nota;
}

$materias_categorias = [];
while ($categoria = $categorias->fetch_assoc()) {
    $materias_categorias[$categoria['materia']][] = $categoria['categoria'];
}

// Cargar cursos, materias, notas y categorías en la vista
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas del Estudiante</title>
</head>
<body>

    <h2>Mis Cursos</h2>
    <ul>
        <?php while ($curso = $cursos->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($curso['nombre']); ?></li>
        <?php endwhile; ?>
    </ul>

    <h2>Materias</h2>
    <ul>
        <?php while ($materia = $materias->fetch_assoc()): ?>
            <li>
                <a href="?materia_id=<?php echo $materia['id']; ?>">
                    <?php echo htmlspecialchars($materia['nombre']); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php if ($materia_id && isset($materias_notas)): ?>
        <h2>Notas de la materia seleccionada</h2>
        <table>
            <thead>
                <tr>
                    <th>Nota</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias_notas as $materia => $notas): ?>
                    <?php foreach ($notas as $nota): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nota['nota']); ?></td>
                            <td><?php echo htmlspecialchars($nota['categoria']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Categorías</h2>
    <ul>
        <?php if ($materias_categorias): ?>
            <?php foreach ($materias_categorias as $materia => $categorias): ?>
                <li><strong><?php echo htmlspecialchars($materia); ?></strong></li>
                <ul>
                    <?php foreach ($categorias as $categoria): ?>
                        <li><?php echo htmlspecialchars($categoria); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No hay categorías disponibles.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
