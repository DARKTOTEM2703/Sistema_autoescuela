<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;
use Backend\Repositories\EstudianteRepository;

// Activar depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    $repo = new EstudianteRepository();
    
    // Obtener todos los estudiantes con sus relaciones
    $estudiantes = $repo->getAllWithRelations();
    
    // Devolver como JSON
    echo json_encode($estudiantes);
} catch (Exception $e) {
    // En caso de error, devolver un objeto JSON con el error
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => 'Error al obtener estudiantes: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}