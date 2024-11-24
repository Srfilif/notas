<?php
include '../database.php';
session_start();
// Array de páginas del sitio con títulos, URL, palabras clave y descripciones
$pages = [
    [
        'title' => 'Inicio',
        'url' => 'index.php',
        'description' => 'Página principal de nuestro sitio web.',
        'keywords' => ['inicio', 'bienvenida', 'home'],
    ],
    [
        'title' => 'Sobre Nosotros',
        'url' => 'about.php',
        'description' => 'Conoce más sobre nuestra historia y equipo.',
        'keywords' => ['sobre', 'nosotros', 'equipo', 'información'],
    ],
    [
        'title' => 'Servicios',
        'url' => 'services.php',
        'description' => 'Descubre los servicios que ofrecemos.',
        'keywords' => ['servicios', 'ofertas', 'productos', 'ayuda'],
    ],
    [
        'title' => 'Contacto',
        'url' => 'contact.php',
        'description' => 'Ponte en contacto con nosotros.',
        'keywords' => ['contacto', 'email', 'soporte', 'ubicación'],
    ],
    [
        'title' => 'Blog',
        'url' => 'blog.php',
        'description' => 'Lee nuestras últimas publicaciones y novedades.',
        'keywords' => ['blog', 'noticias', 'artículos', 'novedades'],
    ],
];

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];

// Buscar coincidencias si se ha ingresado una consulta
if ($searchQuery !== '') {
    foreach ($pages as $page) {
        foreach ($page['keywords'] as $keyword) {
            if (stripos($keyword, $searchQuery) !== false) {
                $results[] = $page;
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Páginas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./public/css/styles.css">
</head>
<body>
<aside>
 <?php include 'componentes/sidebar.php'; ?>
    </aside>
    <?php include 'componentes/topbar.php'; ?>

<main>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Buscar en el Sitio</h1><br>
        <form action="search.php" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Escribe una palabra clave..." value="<?= htmlspecialchars($searchQuery) ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <h3 class="mt-4">Resultados:</h3>
        <?php if (!empty($searchQuery) && empty($results)): ?>
            <p class="text-danger">No se encontraron resultados para "<?= htmlspecialchars($searchQuery) ?>"</p>
        <?php elseif (!empty($results)): ?>
            <ul class="list-group">
                <?php foreach ($results as $result): ?>
                    <li class="list-group-item">
                        <h5>
                            <a href="<?= htmlspecialchars($result['url']) ?>"><?= htmlspecialchars($result['title']) ?></a>
                        </h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($result['description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Escribe algo en la barra de búsqueda para comenzar.</p>
        <?php endif; ?>
    </div>

    </main>
</body>
<?php include 'componentes/footer.php'; ?>
</html>
