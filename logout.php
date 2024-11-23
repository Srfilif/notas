<?php
session_start();

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .logout-container img {
            width: 80px;
            margin-bottom: 20px;
        }

        .logout-container h1 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }

        .logout-container p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .logout-container .btn {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .logout-container .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<?php include 'componentes/header.php'; ?>

<body>

    <center style="padding-top: 5%;" >

        <div   class="logout-container">
            <img src="https://via.placeholder.com/100" alt="Logo">
            <h1>Has cerrado sesión            </h1>
            <p>Gracias por utilizar nuestra plataforma.</p>
            <a href="login.php" class="btn">ir a Login</a>
            <footer>
                <p>&copy; 2024 Filif Company Inc | Developed by <a href="https://srfilif.github.io">SrFilif</a></p>
            </footer>
        </div>
    </center>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'componentes/footer.php'; ?>

</html>