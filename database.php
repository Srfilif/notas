<?php
$host = 'localhost';
$user = 'root'; // Cambia según tu configuración
$password = ''; // Cambia según tu configuración
$dbname = 'ides_notas';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
