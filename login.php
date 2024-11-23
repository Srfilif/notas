<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos de entrada
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']); // Eliminar espacios adicionales

    // Verificar que ambos campos no estén vacíos
    if (empty($email) || empty($password)) {
        echo "<script>Swal.fire('Error', 'Por favor, complete todos los campos.', 'error');</script>";
        exit;
    }

    // Buscar al usuario por su email
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $usuario['password'])) {
            // Iniciar sesión y almacenar datos del usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['email'] = $usuario['email'];

            // Redirigir según el rol del usuario
            echo "<script>window.location.href = 'dashboard/index.php';</script>";
            exit;
        } else {
            echo "<script>Swal.fire('Error', 'Contraseña incorrecta.', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('Error', 'No se encontró ningún usuario con este correo electrónico.', 'error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fc;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-container h5 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            color: #6c757d;
        }

        footer a {
            color: #0d6efd;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body style="background-color: #f8f9fc;">
    <!-- Incluir el header -->
    <?php include 'componentes/header.php'; ?>




    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">


            <h5 class="text-center text-primary">Iniciar sesión</h5>
            <form action="#" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                <div class="text-center mt-3">
                    <hr>
                    <small>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></small>
                </div>
            </form>
        </div>
    </div>


    <!-- Incluir el footer -->
    <footer>
        <?php include 'componentes/footer.php'; ?>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>