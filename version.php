<?php
// Obtener la última versión desde Git
$version = shell_exec('git describe --tags --abbrev=0');
$commitHash = shell_exec('git rev-parse --short HEAD');

// Generar una cadena de versión
$versionText = trim($version) . ' (' . trim($commitHash) . ')';

// Escribir la versión en un archivo PHP
file_put_contents('version.php', "<?php\n\$appVersion = '$versionText';\n");
?>
