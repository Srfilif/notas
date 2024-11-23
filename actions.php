<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notas'])) {
    $materia_id = $_POST['materia_id'];
    $notas = $_POST['notas'];

    foreach ($notas as $estudiante_id => $valores) {
        foreach ($valores as $indice => $nota) {
            if ($nota !== "") {
                // Verificar si ya existe la nota
                $query = $conn->query("SELECT * FROM notas WHERE estudiante_id = $estudiante_id AND materia_id = $materia_id AND id = $indice");
                if ($query->num_rows > 0) {
                    $conn->query("UPDATE notas SET nota = $nota WHERE estudiante_id = $estudiante_id AND materia_id = $materia_id AND id = $indice");
                } else {
                    $conn->query("INSERT INTO notas (estudiante_id, materia_id, nota) VALUES ($estudiante_id, $materia_id, $nota)");
                }
            }
        }
    }
    header("Location: index.php?curso_id={$_POST['curso_id']}&materia_id=$materia_id");
    exit;
}
?>
