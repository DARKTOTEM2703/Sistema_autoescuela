<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $db = Database::getInstance()->getConnection();
    $action = $_GET['action'] ?? 'all';
    
    $response = [
        'success' => true,
        'data' => []
    ];
    
    switch ($action) {
        case 'cursos':
            // Obtener cursos con sus horarios asociados
            $stmt = $db->query("
                SELECT c.*, GROUP_CONCAT(h.id) as horario_ids, 
                GROUP_CONCAT(h.nombre) as horario_nombres,
                GROUP_CONCAT(h.hora_inicio) as horario_inicios,
                GROUP_CONCAT(h.hora_fin) as horario_fines,
                GROUP_CONCAT(h.dias) as horario_dias
                FROM cursos c
                LEFT JOIN curso_horario ch ON c.id = ch.curso_id
                LEFT JOIN horarios h ON ch.horario_id = h.id
                GROUP BY c.id
                ORDER BY c.precio ASC
            ");
            
            $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar los resultados para formatear los horarios
            foreach ($cursos as &$curso) {
                $horarioIds = $curso['horario_ids'] ? explode(',', $curso['horario_ids']) : [];
                $horarioNombres = $curso['horario_nombres'] ? explode(',', $curso['horario_nombres']) : [];
                $horarioInicios = $curso['horario_inicios'] ? explode(',', $curso['horario_inicios']) : [];
                $horarioFines = $curso['horario_fines'] ? explode(',', $curso['horario_fines']) : [];
                $horarioDias = $curso['horario_dias'] ? explode(',', $curso['horario_dias']) : [];
                
                $curso['horarios'] = [];
                for ($i = 0; $i < count($horarioIds); $i++) {
                    if (isset($horarioIds[$i]) && $horarioIds[$i]) {
                        $curso['horarios'][] = [
                            'id' => $horarioIds[$i],
                            'nombre' => $horarioNombres[$i] ?? '',
                            'hora_inicio' => $horarioInicios[$i] ?? '',
                            'hora_fin' => $horarioFines[$i] ?? '',
                            'dias' => $horarioDias[$i] ?? ''
                        ];
                    }
                }
                
                // Limpiar campos temporales
                unset($curso['horario_ids']);
                unset($curso['horario_nombres']);
                unset($curso['horario_inicios']);
                unset($curso['horario_fines']);
                unset($curso['horario_dias']);
            }
            
            $response['data'] = $cursos;
            break;
            
        case 'horarios':
            $stmt = $db->query("SELECT id, nombre, hora_inicio, hora_fin, dias FROM horarios");
            $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default:
            // Obtener todos los datos para la página inicial
            $stmtCursos = $db->query("
                SELECT c.*, GROUP_CONCAT(h.id) as horario_ids, 
                GROUP_CONCAT(h.nombre) as horario_nombres,
                GROUP_CONCAT(h.hora_inicio) as horario_inicios,
                GROUP_CONCAT(h.hora_fin) as horario_fines,
                GROUP_CONCAT(h.dias) as horario_dias
                FROM cursos c
                LEFT JOIN curso_horario ch ON c.id = ch.curso_id
                LEFT JOIN horarios h ON ch.horario_id = h.id
                GROUP BY c.id
                ORDER BY c.precio ASC
            ");
            $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar los cursos como en el caso anterior
            foreach ($cursos as &$curso) {
                $horarioIds = $curso['horario_ids'] ? explode(',', $curso['horario_ids']) : [];
                $horarioNombres = $curso['horario_nombres'] ? explode(',', $curso['horario_nombres']) : [];
                $horarioInicios = $curso['horario_inicios'] ? explode(',', $curso['horario_inicios']) : [];
                $horarioFines = $curso['horario_fines'] ? explode(',', $curso['horario_fines']) : [];
                $horarioDias = $curso['horario_dias'] ? explode(',', $curso['horario_dias']) : [];
                
                $curso['horarios'] = [];
                for ($i = 0; $i < count($horarioIds); $i++) {
                    if (isset($horarioIds[$i]) && $horarioIds[$i]) {
                        $curso['horarios'][] = [
                            'id' => $horarioIds[$i],
                            'nombre' => $horarioNombres[$i] ?? '',
                            'hora_inicio' => $horarioInicios[$i] ?? '',
                            'hora_fin' => $horarioFines[$i] ?? '',
                            'dias' => $horarioDias[$i] ?? ''
                        ];
                    }
                }
                
                // Limpiar campos temporales
                unset($curso['horario_ids']);
                unset($curso['horario_nombres']);
                unset($curso['horario_inicios']);
                unset($curso['horario_fines']);
                unset($curso['horario_dias']);
            }
            
            $stmtHorarios = $db->query("SELECT id, nombre, hora_inicio, hora_fin, dias FROM horarios");
            $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);
            
            $response['data'] = [
                'cursos' => $cursos,
                'horarios' => $horarios
            ];
            break;
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}