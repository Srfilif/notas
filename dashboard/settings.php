<?php
// Iniciar sesión
session_start();

// Aquí deberías incluir la conexión a la base de datos si es necesario
 include '../database.php';

// Obtener el ID del usuario desde la sesión
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

// Verificar si el usuario está autenticado
if ($usuario_id == 0) {
    echo "Acceso no autorizado.";
    exit;
}

// Manejo del formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    
    // Validar los datos
    if (empty($nombre) || empty($email)) {
        $error_message = "El nombre y el correo electrónico son obligatorios.";
    } else {
        // Actualizar los datos en la base de datos
        $update_query = "UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssi", $nombre, $email, $telefono, $direccion, $usuario_id);
        
        if ($stmt->execute()) {
            $success_message = "Perfil actualizado con éxito.";
        } else {
            $error_message = "Error al actualizar el perfil. Inténtelo nuevamente.";
        }
    }
}

// Obtener los datos actuales del usuario
$user_query = "SELECT nombre, email, telefono, direccion, avatar FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Obtener los valores del usuario
$nombre = isset($user_data['nombre']) ? $user_data['nombre'] : '';
$email = isset($user_data['email']) ? $user_data['email'] : '';
$telefono = isset($user_data['telefono']) ? $user_data['telefono'] : '';
$direccion = isset($user_data['direccion']) ? $user_data['direccion'] : '';
$avatar = isset($user_data['avatar']) ? $user_data['avatar'] : 'https://via.placeholder.com/150';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Perfil</title>
    <!-- Agregar los enlaces de Bootstrap para el estilo -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/styles.css">
</head>

<body>
<aside>
 <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>


    <main> 
    <!-- Contenido principal -->
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-4 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px" src="<?= htmlspecialchars($avatar) ?>" alt="Avatar de <?= htmlspecialchars($nombre) ?>">
                    <span class="font-weight-bold"><?= htmlspecialchars($nombre) ?></span>
                    <span class="text-black-50"><?= htmlspecialchars($email) ?></span>
                </div>
            </div>

            <div class="col-md-6 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">Configuración de Perfil</h4>
                    </div>
                    <?php if (isset($success_message)) { ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php } elseif (isset($error_message)) { ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php } ?>
                    <form method="POST">
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="labels">Nombre</label>
                                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="labels">Correo Electrónico</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="labels">Número de Teléfono</label>
                                <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($telefono) ?>" placeholder="Introduce tu número de teléfono">
                            </div>
                            <div class="col-md-12">
                                <label class="labels">Dirección</label>
                                <input type="text" class="form-control" name="direccion" value="<?= htmlspecialchars($direccion) ?>" placeholder="Introduce tu dirección">
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <button class="btn btn-primary profile-button" type="submit">Guardar Perfil</button>
                        </div>
                    </form>
                </div>
            </div>

            
        </div>
    </div>
    </main>
    <?php include 'componentes/footer.php'; ?>
    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
