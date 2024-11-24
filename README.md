# Sistema de Notas

¡Bienvenido al **Sistema de Notas**! Este proyecto es una plataforma web diseñada para gestionar notas académicas de manera eficiente y profesional.

## Funcionalidades

- Gestión de perfiles de usuario.
- Edición y almacenamiento de notas académicas.
- Sistema de autenticación seguro.
- Panel de administración interactivo con diseño moderno.
- Módulos de configuración personalizables.
- Visualización profesional de información.

## Tecnologías utilizadas

- **Frontend**: HTML, CSS (Bootstrap 5), JavaScript.
- **Backend**: PHP 8.
- **Base de datos**: MySQL.
- **Librerías adicionales**: SweetAlert2, DataTables.

## Requisitos del sistema

- Servidor web con soporte para PHP 8 o superior.
- MySQL 5.7 o superior.
- Composer instalado (opcional para manejar dependencias).

## Instalación

1. Clona el repositorio:
    ```bash
    git clone https://github.com/srfilif/notas.git
    ```
2. Configura la base de datos:
   - Crea una base de datos en MySQL llamada `sistema_notas`.
   - Importa el archivo SQL desde el release: [Descargar base de datos](https://github.com/srfilif/notas/releases/latest/download/sistema_notas.sql).

3. Configura el archivo `.env` (si aplicable) o edita `config.php` para establecer las credenciales de la base de datos:
    ```php
    $db_host = 'localhost';
    $db_user = 'tu_usuario';
    $db_pass = 'tu_contraseña';
    $db_name = 'ides_notas';
    ```

4. Inicia el servidor local:
    ```bash
    php -S localhost:8000
    ```

5. Accede a la aplicación en tu navegador: [http://localhost:8000](http://localhost:8000).

## Releases

¡Descarga la última versión estable del sistema y la base de datos desde los releases de GitHub!

- **Última versión del código fuente**: [Descargar](https://github.com/srfilif/notas/releases/latest)
- **Base de datos SQL**: [Descargar](https://github.com/srfilif/notas/releases/latest/download/sistema_notas.sql)

## Contribuciones

¡Las contribuciones son bienvenidas! Si tienes ideas, encuentra un error o deseas mejorar algo, no dudes en abrir un **issue** o enviar un **pull request**.

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.

---

Desarrollado con ❤️ por [Andrés Felipe Ángel Imbacuán](https://github.com/srfilif).
