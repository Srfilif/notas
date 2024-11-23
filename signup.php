<?php
session_start(); // Iniciar sesión para usar variables de sesión
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $rol = $_POST['rol']; // El rol será 'estudiante' o 'profesor'

    // Verificar si el email ya está registrado
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Este correo electrónico ya está registrado.'];
    } else {
        // Insertar nuevo usuario en la base de datos
        $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $nombre, $email, $password, $rol);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Registro exitoso. Ahora puedes iniciar sesión.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al registrar. Intenta nuevamente.'];
        }
    }

    // Redirigir a la misma página para mostrar el mensaje
    header("Location: register.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Estilo personalizado -->
    <style>
        body {
            background-color: #f8f9fc;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #375a7f;
        }

        .text-primary {
            color: #0d6efd !important;
        }
        
        .register-container h5{
            font-weight: bold;

        }
    </style>


</head>

<body>
<?php include 'componentes/header.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <div class="register-container">

            <h5 class="text-center text-primary">Crear Cuenta</h5>
            </div>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Escribe tu nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico:</label>
                    <input type="email" class="form-control" name="email" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol:</label>
                    <select name="rol" class="form-select">
                        <option value="estudiante">Estudiante</option>
                        <option value="profesor">Profesor</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar</button>
            </form>
            <hr>
            <div class="text-center">
                <small>¿Ya tienes una cuenta? <a href="login.php" class="text-primary">Inicia sesión</a></small>
            </div>
        </div>
    </div>
    <?php
    // Mostrar mensaje si existe en la sesión
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        echo "<script>
            Swal.fire({
                icon: '{$mensaje['tipo']}',
                title: '{$mensaje['texto']}'
            });
        </script>";
        unset($_SESSION['mensaje']); // Eliminar mensaje después de mostrarlo
    }
    ?>
</body>
<?php include 'componentes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>