<?php
session_start();
require_once '../../../backend/autoload.php';

use Backend\Services\AuthService;

$authService = new AuthService();
if (!$authService->isAuthenticated()) {
    header('Location: ../login/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicializar Base de Datos - AutoEscuela Segura</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="css/tools.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="icon"><i class="fas fa-car"></i></span>
                    <h1>Panel Admin</h1>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../index.php"><i class="fas fa-user-graduate"></i> Estudiantes</a></li>
                    <li><a href="../cursos.php"><i class="fas fa-graduation-cap"></i> Cursos</a></li>
                    <li><a href="../horarios.php"><i class="fas fa-clock"></i> Horarios</a></li>
                    <li><a href="../configuracion.php"><i class="fas fa-cog"></i> Configuración</a></li>
                    <li class="nav-section">
                        <span>Herramientas</span>
                    </li>
                    <li><a href="password_manager.php"><i class="fas fa-key"></i> Gestión Contraseñas</a></li>
                    <li class="active"><a href="database_init.php"><i class="fas fa-database"></i> Inicializar BD</a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="page-title">
                    <i class="fas fa-database"></i>
                    <h2>Inicializar Base de Datos</h2>
                </div>
                <div class="user-info">
                    <span><?php echo $_SESSION['usuario_nombre'] ?? 'Admin'; ?></span>
                    <a href="../logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </header>

            <?php if (isset($_GET['result']) && $_GET['result'] == 'success'): ?>
            <div class="notification-enhanced success-enhanced animate-pulse">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="notification-content">
                    <h3>¡Operación Finalizada con Éxito!</h3>
                    <p>La base de datos del sistema ha sido inicializada correctamente con todos los parámetros
                        predeterminados.</p>
                    <p class="timestamp"><i class="far fa-clock"></i> <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-circle"></i>
                Error: <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <div class="content-wrapper">
                <div class="tool-container">
                    <div class="warning-card">
                        <div class="warning-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>¡Atención! Esta acción es irreversible</h3>
                        <p>Al inicializar la base de datos se eliminarán todos los datos existentes y se crearán nuevas
                            tablas con datos iniciales. Esta acción no se puede deshacer.</p>

                        <div class="action-buttons">
                            <a href="../../../backend/database/init_db.php?confirm_init=1" class="btn-danger">
                                <i class="fas fa-database"></i> Inicializar Base de Datos
                            </a>
                            <a href="../configuracion.php" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Configuración
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>