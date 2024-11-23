<?php
session_start();
include 'database.php';

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    header("Location: error.php");
    exit;
}

// Obtener el ID de la materia seleccionada (si lo hay)
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : null;

// Obtener las materias del estudiante
$usuario_id = $_SESSION['usuario_id'];
$materias = $conn->query("SELECT DISTINCT m.id, m.nombre
                          FROM materias m
                          JOIN notas n ON m.id = n.materia_id
                          WHERE n.estudiante_id = $usuario_id");

// Filtrar las notas y categorías según la materia seleccionada
$notas_query = "SELECT n.nota, m.nombre AS materia, c.nombre AS categoria, m.id AS materia_id
                FROM notas n
                JOIN materias m ON n.materia_id = m.id
                JOIN categorias_notas c ON n.categoria_id = c.id
                WHERE n.estudiante_id = $usuario_id";

$categorias_query = "SELECT c.nombre AS categoria, m.nombre AS materia, m.id AS materia_id
                     FROM categorias_notas c
                     JOIN materias m ON c.materia_id = m.id";

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Notas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h1 class="text-center text-primary">Mis Notas</h1>

        <!-- Formulario para seleccionar la materia -->
        <form method="GET" action="" class="mb-4">
            <div class="mb-3">
                <label for="materia" class="form-label">Seleccionar Materia:</label>
                <select name="materia_id" id="materia" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Ver Todas --</option>
                    <?php while ($materia = $materias->fetch_assoc()): ?>
                        <option value="<?php echo $materia['id']; ?>" <?php echo $materia_id == $materia['id'] ? 'selected' : ''; ?>>
                            <?php echo $materia['nombre']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <!-- Mostrar las tablas de notas -->
        <?php foreach ($materias_notas as $materia_nombre => $notas_materia): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <strong><?php echo strtoupper($materia_nombre); ?></strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="table-primary">
                                <th>Asignatura</th>
                                <?php foreach ($materias_categorias[$materia_nombre] as $categoria): ?>
                                    <th><?php echo $categoria; ?></th>
                                <?php endforeach; ?>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $materia_nombre; ?></td>
                                <?php 
                                $suma_notas = 0;
                                $cantidad_notas = 0;
                                foreach ($materias_categorias[$materia_nombre] as $categoria): ?>
                                    <td>
                                        <?php
                                        $nota_en_categoria = '';
                                        foreach ($notas_materia as $nota) {
                                            if ($nota['categoria'] === $categoria) {
                                                $nota_en_categoria = $nota['nota'];
                                                $suma_notas += $nota['nota'];
                                                $cantidad_notas++;
                                                break;
                                            }
                                        }
                                        echo $nota_en_categoria ? $nota_en_categoria : '-';
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <?php echo $cantidad_notas > 0 ? number_format($suma_notas / $cantidad_notas, 2) : '-'; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
