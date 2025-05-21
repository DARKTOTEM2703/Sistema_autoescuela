<?php
session_start();
require_once '../autoload.php';

use Backend\Services\AuthService;
use Backend\Config\Database;

// Verificar autenticación
$authService = new AuthService();
if (!$authService->isAuthenticated()) {
    header('Location: ../../frontend/admin/login/index.php');
    exit;
}

// Verificar nivel de permisos
if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] < 3) {
    header('Location: ../../frontend/admin/configuracion.php?error=permisos');
    exit;
}

// Procesar la inicialización solo cuando se envía el formulario
if (isset($_POST['confirm_init']) || isset($_GET['confirm_init'])) {
    try {
        // Aquí va el código para inicializar la base de datos
        $db = Database::getInstance()->getConnection();
        
        // Primero eliminar todas las tablas existentes
        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Lista de tablas a eliminar (ajusta según tus tablas)
        $tables = ['cursos', 'curso_horario', 'estudiantes', 'horarios', 'tipos_auto', 'usuarios'];
        
        foreach ($tables as $table) {
            $db->exec("DROP TABLE IF EXISTS $table");
        }
        
        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Ruta al archivo SQL
        $sqlFile = __DIR__ . '/autoescuela_db.sql';

        if (!file_exists($sqlFile)) {
            throw new Exception('El archivo SQL no existe');
        }

        // Leer el archivo SQL
        $sql = file_get_contents($sqlFile);

        // Ejecutar las consultas SQL
        $db->exec($sql);

        // Redireccionar con mensaje de éxito
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../../frontend/admin/tools/database_init.php?result=success';
        header('Location: ' . $redirect);
        exit;
    } catch (Exception $e) {
        // En caso de error, redireccionar con mensaje de error
        header('Location: ../../frontend/admin/tools/database_init.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Si se accede directamente sin confirmar, redirigir al frontend
header('Location: ../../frontend/admin/tools/database_init.php');
exit;
// Fin del script