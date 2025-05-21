<?php
// Incluir el autoloader personalizado
require_once '../../backend/autoload.php';

use Backend\Services\AuthService;

session_start();

$authService = new AuthService();
$authService->logout();

// Redirigir a la página de inicio
header('Location: ../landing/index.php');
exit;