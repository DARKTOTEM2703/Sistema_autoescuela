<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;
use Backend\Repositories\EstudianteRepository;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar solicitudes OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $repo = new EstudianteRepository();

    // Determinar la acción basada en el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Obtener todos los horarios o uno específico
            if (isset($_GET['id'])) {
                $horario = $repo->getHorarioById($_GET['id']);
                if ($horario) {
                    echo json_encode($horario);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => true, 'mensaje' => 'Horario no encontrado']);
                }
            } else {
                $horarios = $repo->getAllHorarios();
                echo json_encode($horarios);
            }
            break;

        case 'POST':
            // Crear un nuevo horario
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos
            if (!isset($data['nombre']) || !isset($data['hora_inicio']) || !isset($data['hora_fin']) || !isset($data['dias'])) {
                http_response_code(400);
                echo json_encode(['error' => true, 'mensaje' => 'Datos incompletos']);
                exit;
            }

            try {
                $horarioId = $repo->createHorario($data);
                echo json_encode(['success' => true, 'id' => $horarioId, 'mensaje' => 'Horario creado correctamente']);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => true, 'mensaje' => 'Error al crear horario: ' . $e->getMessage()]);
            }
            break;

        case 'PUT':
            // Actualizar un horario existente
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => true, 'mensaje' => 'ID de horario no especificado']);
                exit;
            }

            try {
                $resultado = $repo->updateHorario($_GET['id'], $data);
                if ($resultado) {
                    echo json_encode(['success' => true, 'mensaje' => 'Horario actualizado correctamente']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => true, 'mensaje' => 'No se encontró el horario']);
                }
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => true, 'mensaje' => 'Error al actualizar: ' . $e->getMessage()]);
            }
            break;

        case 'DELETE':
            // Eliminar un horario
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => true, 'mensaje' => 'ID de horario no especificado']);
                exit;
            }

            try {
                $resultado = $repo->deleteHorario($_GET['id']);
                echo json_encode(['success' => true, 'mensaje' => 'Horario eliminado correctamente']);
            } catch (\Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => true, 'mensaje' => $e->getMessage()]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => true, 'mensaje' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}