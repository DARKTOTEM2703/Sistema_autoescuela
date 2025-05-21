<?php
session_start();

// Si ya está autenticado, redirigir al dashboard
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true) {
    header('Location: ../index.php');
    exit;
}

// Verificar si hay errores de login
$errorLogin = isset($_SESSION['error_login']) ? $_SESSION['error_login'] : null;
unset($_SESSION['error_login']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AutoEscuela Segura</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="css/login.css">
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
                <li><a href="../../landing/index.php#inicio">Inicio</a></li>
                <li><a href="../../landing/index.php#nosotros">Nosotros</a></li>
                <li><a href="../../landing/index.php#cursos">Cursos</a></li>
                <li><a href="../../landing/index.php#contacto">Contacto</a></li>
                <li><a href="index.php" class="login">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="login-container">
            <div class="login-icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <h2 class="login-title">Iniciar Sesión</h2>

            <?php if ($errorLogin): ?>
            <div class="error-message">
                <?php echo $errorLogin; ?>
            </div>
            <?php endif; ?>

            <form action="../../../backend/auth/login.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña"
                        required>
                </div>

                <button type="submit" class="login-btn">Iniciar Sesión</button>
            </form>

            <div class="admin-info">
                <p>Para acceder al panel de administración, usa:</p>
                <div class="admin-credentials">
                    Usuario: admin / Contraseña: admin123
                </div>
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
                    <li><a href="../../landing/index.php#inicio">Inicio</a></li>
                    <li><a href="../../landing/index.php#nosotros">Nosotros</a></li>
                    <li><a href="../../landing/index.php#cursos">Cursos</a></li>
                    <li><a href="../../landing/index.php#contacto">Contacto</a></li>
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