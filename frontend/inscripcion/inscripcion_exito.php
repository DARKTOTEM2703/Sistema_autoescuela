<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción Exitosa - AutoEscuela Segura</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="inscripcion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <header>
        <div class="logo">
            <span class="icon"><i class="fas fa-car"></i></span>
            <h1>AutoEscuela Segura</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Nosotros</a></li>
                <li><a href="#">Cursos</a></li>
                <li><a href="#">Contacto</a></li>
                <li><a href="#" class="login">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="inscripcion-main">
        <div class="exito-container">
            <i class="fas fa-check-circle icon-exito"></i>
            <h2 class="titulo-exito">¡Inscripción Exitosa!</h2>
            <p class="mensaje-exito">
                <?php
                session_start();
                echo $_SESSION['exito'] ?? 'Su solicitud de inscripción ha sido enviada correctamente. Nos pondremos en contacto con usted pronto.';
                unset($_SESSION['exito']);
                ?>
            </p>
            <div class="botones-container">
                <a href="../landing/index.php" class="btn btn-secondary">Volver al inicio</a>
                <a href="#" class="btn btn-primary">Ver mis cursos</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <div class="footer-logo">
                    <span class="icon"><i class="fas fa-car"></i></span>
                    <h3>AutoEscuela Segura</h3>
                </div>
                <p>Formando conductores responsables desde 2005. Nuestra misión es enseñar a conducir de manera segura y
                    responsable.</p>
            </div>

            <div class="footer-column">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Nosotros</a></li>
                    <li><a href="#">Cursos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Servicios</h4>
                <ul>
                    <li><a href="#">Cursos para principiantes</a></li>
                    <li><a href="#">Cursos avanzados</a></li>
                    <li><a href="#">Clases particulares</a></li>
                    <li><a href="#">Evaluaciones</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Política de privacidad</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 AutoEscuela Segura. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>