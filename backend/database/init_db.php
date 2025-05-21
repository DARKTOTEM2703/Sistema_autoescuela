<?php
session_start();
require_once '../../../backend/autoload.php';
use Backend\Services\AuthService;

$authService = new AuthService();
if (!$authService->isAuthenticated()) {
    header('Location: ../login/index.php');
    exit;
}

// Verificar nivel de permisos (opcional)
if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] < 3) {
    header('Location: ../configuracion.php?error=permisos');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .tool-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .warning-card {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-left: 5px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .warning-icon {
            font-size: 48px;
            color: #ffc107;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
    </style>
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
                    <li class="active"><a href="database_init.php"><i class="fas fa-database"></i> Inicializar BD</a></li>
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
            
            <div class="content-wrapper">
                <div class="tool-container">
                    <?php if (isset($_GET['result']) && $_GET['result'] === 'success'): ?>
                        <div class="success-card" style="background-color: #d4edda; border: 1px solid #c3e6cb; border-left: 5px solid #28a745; padding: 20px; border-radius: 5px;">
                            <div class="success-icon" style="font-size: 48px; color: #28a745; text-align: center; margin-bottom: 15px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3>¡Base de datos inicializada correctamente!</h3>
                            <p>La base de datos ha sido recreada con los valores por defecto.</p>
                            <div class="action-buttons">
                                <a href="../configuracion.php" class="btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a Configuración
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="warning-card">
                            <div class="warning-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3>¡Atención! Esta acción es irreversible</h3>
                            <p>Al inicializar la base de datos se eliminarán todos los datos existentes y se crearán nuevas tablas con datos iniciales. Esta acción no se puede deshacer.</p>
                            
                            <form id="confirmation-form" action="../../../backend/database/init_db.php" method="post" onsubmit="return confirm('¿Estás seguro de querer inicializar la base de datos? TODOS LOS DATOS SERÁN ELIMINADOS.');">
                                <input type="hidden" name="confirm_init" value="1">
                                <input type="hidden" name="redirect" value="../../../frontend/admin/tools/database_init.php?result=success">
                                
                                <div class="action-buttons">
                                    <button type="submit" class="btn-danger">
                                        <i class="fas fa-database"></i> Inicializar Base de Datos
                                    </button>
                                    <a href="../configuracion.php" class="btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>