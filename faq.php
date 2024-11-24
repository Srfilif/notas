
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes - Sistema de Notas</title>
    <!-- Agregar los enlaces de Bootstrap para el estilo -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Barra lateral y Topbar -->


    <!-- Barra lateral y Topbar -->
    <?php include 'componentes/header.php'; ?>
    <main> 
    <!-- Contenido principal -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Preguntas Frecuentes</h2>

        <!-- Preguntas y respuestas -->
        <div class="accordion" id="faqAccordion">

            <!-- Pregunta 1 -->
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            ¿Cómo puedo registrar mis calificaciones?
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                    <div class="card-body">
                        Para registrar las calificaciones, los profesores deben ingresar al sistema con su cuenta, acceder a la sección de "Asignaturas" y luego seleccionar la materia en la cual desean agregar las calificaciones. Allí, podrán introducir las notas de los estudiantes de forma rápida y sencilla.
                    </div>
                </div>
            </div>

            <!-- Pregunta 2 -->
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            ¿Cómo puedo ver mis notas como estudiante?
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                    <div class="card-body">
                        Los estudiantes pueden ver sus calificaciones accediendo a la sección de "Mis Notas". Ahí podrán consultar las calificaciones por asignatura y semestre. Si alguna calificación está pendiente, se mostrará como "No calificada" hasta que el profesor la registre.
                    </div>
                </div>
            </div>

            <!-- Pregunta 3 -->
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            ¿Puedo descargar mis calificaciones?
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                    <div class="card-body">
                        Sí, puedes descargar tus calificaciones en formato PDF o Excel desde la sección "Mis Notas". Simplemente selecciona el semestre o las asignaturas correspondientes y haz clic en "Descargar". El sistema generará el archivo con tus calificaciones.
                    </div>
                </div>
            </div>

            <!-- Pregunta 4 -->
            <div class="card">
                <div class="card-header" id="headingFour">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            ¿Qué hago si una calificación está incorrecta?
                        </button>
                    </h5>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                    <div class="card-body">
                        Si encuentras un error en tu calificación, por favor contacta a tu profesor o administrador del sistema. Ellos podrán verificar la información y realizar los ajustes necesarios. Si eres un profesor, puedes editar las calificaciones directamente en la sección de gestión de asignaturas.
                    </div>
                </div>
            </div>

            <!-- Pregunta 5 -->
            <div class="card">
                <div class="card-header" id="headingFive">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            ¿Cómo puedo cambiar mi contraseña?
                        </button>
                    </h5>
                </div>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                    <div class="card-body">
                        Para cambiar tu contraseña, ve a la sección "Configuración de Perfil" y selecciona "Cambiar Contraseña". Luego, sigue los pasos indicados para actualizar tu contraseña. Asegúrate de usar una contraseña segura.
                    </div>
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
