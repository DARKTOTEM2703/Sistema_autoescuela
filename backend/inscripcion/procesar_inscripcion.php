<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Services\EstudianteService;
use Backend\Services\ArchivoHandler;
use Backend\Services\ValidadorInscripcion;
use Backend\Repositories\EstudianteRepository;

// Activar la visualización de errores durante desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Registrar errores en un archivo para depuración
$logFile = __DIR__ . '/../../logs/inscripcion_errors.log';
$directorio = dirname($logFile);
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}

// Función para guardar logs
function logError($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        logError("Procesando formulario POST: " . print_r($_POST, true));
        logError("Archivos recibidos: " . print_r($_FILES, true));

        // Crear instancias de servicios
        $repository = new EstudianteRepository();
        logError("Repository creado");

        $validador = new ValidadorInscripcion();
        logError("Validador creado");

        $archivoHandler = new ArchivoHandler();
        logError("ArchivoHandler creado");

        // Instanciar el servicio con las dependencias
        $estudianteService = new EstudianteService($repository, $validador, $archivoHandler);
        logError("EstudianteService creado");

        // Procesar el formulario y guardar estudiante
        $resultado = $estudianteService->createEstudiante($_POST, $_FILES);
        logError("Resultado: " . print_r($resultado, true));

        // Configurar cabecera para respuesta JSON
        header('Content-Type: application/json');

        // Si la inscripción es exitosa, guardar mensaje en sesión
        if ($resultado['success']) {
            session_start();
            $_SESSION['exito'] = $resultado['mensaje'];
        }

        echo json_encode($resultado);
    } catch (Exception $e) {
        // Registrar error detallado
        logError("ERROR: " . $e->getMessage());
        logError("Traza: " . $e->getTraceAsString());

        // Si ocurre un error, devolver mensaje detallado
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errores' => [$e->getMessage()],
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
} else {
    // Si no se envió por POST, devolver error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'errores' => ['Método de solicitud inválido']
    ]);
}