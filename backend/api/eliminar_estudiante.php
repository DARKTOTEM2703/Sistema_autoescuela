<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Services\EstudianteService;
use Backend\Services\ArchivoHandler;
use Backend\Services\ValidadorInscripcion;
use Backend\Repositories\EstudianteRepository;

header('Content-Type: application/json');

// Verificar método HTTP y la existencia del ID
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);

        // Crear instancias necesarias en orden correcto
        $repository = new EstudianteRepository();
        $validador = new ValidadorInscripcion();
        $archivoHandler = new ArchivoHandler();

        // Instanciar el servicio con las dependencias
        $estudianteService = new EstudianteService($repository, $validador, $archivoHandler);

        // Eliminar estudiante
        $resultado = $estudianteService->deleteEstudiante($id);

        // Devolver respuesta
        echo json_encode($resultado);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método incorrecto o ID no proporcionado'
    ]);
}