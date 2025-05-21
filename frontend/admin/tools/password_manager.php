<?php
// Proteger con autenticación
session_start();
require_once '../../../backend/autoload.php';
use Backend\Services\AuthService;

$authService = new AuthService();
if (!$authService->isAuthenticated() || !$authService->hasRole('administrador')) {
    header('Location: ../login/index.php');
    exit;
}

// Incluir la herramienta desde el backend
include_once '../../../backend/utils/password_tool.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contraseñas - AutoEscuela Segura</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="css/password_tool_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <!-- Código del sidebar -->
        </aside>
        
        <!-- Contenido principal -->
        <main class="main-content">
            <header class="content-header">
                <!-- Cabecera -->
            </header>
            
            <div class="content-wrapper">
                <div class="password-tool-container">
                    <?php renderPasswordToolUI(); ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="js/password_tool.js"></script>
</body>
</html>