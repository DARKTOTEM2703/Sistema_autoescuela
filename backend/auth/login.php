<?php
// Incluir el autoloader personalizado
require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;
use Backend\Services\AuthService;

// Iniciar sesión
session_start();

// Verificar si hay datos de POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener las credenciales
    $username = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    // Validación básica de entradas
    if (empty($username) || empty($password)) {
        $_SESSION['error_login'] = 'Por favor, complete todos los campos';
        header('Location: ../../frontend/admin/login/index.php');
        exit;
    }

    // Crear instancia del servicio de autenticación
    $authService = new AuthService();

    try {
        // Intentar login
        $loginSuccess = $authService->login($username, $password);

        if ($loginSuccess) {
            // Redirigir al dashboard
            header('Location: ../../frontend/admin/index.php');
            exit;
        } else {
            // Si falla, redirigir de nuevo al login con mensaje de error
            $_SESSION['error_login'] = 'Usuario o contraseña incorrectos';
            header('Location: ../../frontend/admin/login/index.php');
            exit;
        }
    } catch (\Exception $e) {
        // Capturar cualquier error y mostrar un mensaje genérico
        $_SESSION['error_login'] = 'Error en el sistema. Por favor, inténtelo más tarde.';
        // En un entorno de desarrollo, podrías querer mostrar el error real:
        // $_SESSION['error_login'] = 'Error: ' . $e->getMessage();
        header('Location: ../../frontend/admin/login/index.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al formulario de login
    header('Location: ../../frontend/admin/login/index.php');
    exit;
}