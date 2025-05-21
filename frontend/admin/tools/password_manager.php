<?php
// Evitar cualquier salida antes de definir encabezados
ob_start();

// Proteger con autenticación
session_start();
require_once '../../../backend/autoload.php';

use Backend\Services\AuthService;

$authService = new AuthService();
if (!$authService->isAuthenticated() || !$authService->hasRole('administrador')) {
    header('Location: ../login/index.php');
    exit;
}

// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Limpiar cualquier salida previa
    ob_clean();
    
    // Establecer encabezados para prevenir caché y especificar JSON
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    try {
        include_once '../../../backend/utils/password_tool.php';
        $passwordTool = new PasswordTool();
        $result = $passwordTool->processRequest();
        
        // Si processRequest no envía la respuesta, lo hacemos aquí
        if (!isset($result['sent'])) {
            echo json_encode($result);
        }
    } catch (Exception $e) {
        // Enviar error como JSON
        echo json_encode([
            'success' => false,
            'mensaje' => 'Error: ' . $e->getMessage()
        ]);
    }
    
    exit; // Terminar la ejecución
}

// Continuar con el HTML para solicitudes normales
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contraseñas - AutoEscuela Segura</title>
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
                    <li>
                        <a href="../index.php"><i class="fas fa-user-graduate"></i> Estudiantes</a>
                    </li>
                    <li>
                        <a href="../cursos.php"><i class="fas fa-graduation-cap"></i> Cursos</a>
                    </li>
                    <li>
                        <a href="../horarios.php"><i class="fas fa-clock"></i> Horarios</a>
                    </li>
                    <li>
                        <a href="../configuracion.php"><i class="fas fa-cog"></i> Configuración</a>
                    </li>
                    <!-- Sección de herramientas -->
                    <li class="nav-section">
                        <span>Herramientas</span>
                    </li>
                    <li class="active">
                        <a href="password_manager.php"><i class="fas fa-key"></i> Gestión Contraseñas</a>
                    </li>
                    <li>
                        <a href="database_init.php"><i class="fas fa-database"></i> Inicializar BD</a>
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
                    <i class="fas fa-key"></i>
                    <h2>Gestión de Contraseñas</h2>
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
                    <div class="password-warning">
                        <strong>Advertencia:</strong> Esta herramienta debe usarse solo por administradores del sistema
                        y debe estar protegida.
                    </div>

                    <div class="password-card">
                        <h3 class="section-title">Gestión de Contraseñas</h3>
                        <form id="passwordForm" class="password-form">
                            <div class="form-group">
                                <label for="action">Acción:</label>
                                <select id="action" name="action" class="form-control" required>
                                    <option value="generar" selected>Generar Hash</option>
                                    <option value="verificar">Verificar Contraseña</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="usernameField" style="display:none;">
                                <label for="username">Usuario:</label>
                                <input type="text" id="username" name="username" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-key"></i> Ejecutar
                            </button>
                        </form>

                        <div id="result" class="password-result">
                            <!-- Aquí se mostrará el resultado -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/password_tool.js"></script>
</body>

</html>