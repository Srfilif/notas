<?php
include '../database.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'profesor') {
    header("Location: ../error.php?errorcode=403");
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

    // Obtener estudiantes del curso seleccionado (usando la tabla cursos_actuales)
    $estudiantes = $conn->query("SELECT u.id, u.nombre FROM usuarios u
                                INNER JOIN cursos_actuales ca ON u.id = ca.usuario_id
                                WHERE ca.curso_id = $curso_id");
}

// Obtener las categorías de notas de la materia seleccionada
if ($materia_id) {
    $categorias_notas = $conn->query("SELECT * FROM categorias_notas WHERE materia_id = $materia_id");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_notas'])) {
    foreach ($_POST['notas'] as $usuario_id => $categorias) {
        foreach ($categorias as $categoria_id => $nota) {
            if ($nota >= 0 && $nota <= 5) {
                $existe_nota = $conn->query("SELECT id FROM notas WHERE usuario_id = $usuario_id AND categoria_id = $categoria_id");
                if ($existe_nota->num_rows > 0) {
                    $conn->query("UPDATE notas SET nota = $nota WHERE usuario_id = $usuario_id AND categoria_id = $categoria_id");
                } else {
                    $conn->query("INSERT INTO notas (usuario_id, categoria_id, materia_id, nota) VALUES ($usuario_id, $categoria_id, $materia_id, $nota)");
                }
            }
        }
    }
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Éxito!',
            text: 'Notas guardadas correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    });
</script>";

}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Notas</title>
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
                                            $nota_query = $conn->query("SELECT nota FROM notas WHERE usuario_id = {$estudiante['id']} AND categoria_id = {$categoria['id']}");
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
                    <button id="guardarNotas" name="guardar_notas" class="btn btn-success">Guardar Notas</button>
                
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'componentes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


