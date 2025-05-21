<?php
require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $db = Database::getInstance()->getConnection();

    // Determinar el método HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Si se proporciona un ID, obtener un curso específico
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $db->prepare("
                    SELECT c.*, GROUP_CONCAT(h.id) as horario_ids, 
                    GROUP_CONCAT(h.nombre) as horario_nombres,
                    GROUP_CONCAT(h.hora_inicio) as horario_inicios,
                    GROUP_CONCAT(h.hora_fin) as horario_fines,
                    GROUP_CONCAT(h.dias) as horario_dias
                    FROM cursos c
                    LEFT JOIN curso_horario ch ON c.id = ch.curso_id
                    LEFT JOIN horarios h ON ch.horario_id = h.id
                    WHERE c.id = :id
                    GROUP BY c.id
                ");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $curso = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($curso) {
                    // Procesar horarios
                    $curso['horarios'] = [];
                    if ($curso['horario_ids']) {
                        $horarioIds = explode(',', $curso['horario_ids']);
                        $horarioNombres = explode(',', $curso['horario_nombres']);
                        $horarioInicios = explode(',', $curso['horario_inicios']);
                        $horarioFines = explode(',', $curso['horario_fines']);
                        $horarioDias = explode(',', $curso['horario_dias']);

                        for ($i = 0; $i < count($horarioIds); $i++) {
                            $curso['horarios'][] = [
                                'id' => $horarioIds[$i],
                                'nombre' => $horarioNombres[$i],
                                'hora_inicio' => $horarioInicios[$i],
                                'hora_fin' => $horarioFines[$i],
                                'dias' => $horarioDias[$i]
                            ];
                        }
                    }

                    // Limpiar campos temporales
                    unset($curso['horario_ids']);
                    unset($curso['horario_nombres']);
                    unset($curso['horario_inicios']);
                    unset($curso['horario_fines']);
                    unset($curso['horario_dias']);

                    echo json_encode($curso);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'mensaje' => 'Curso no encontrado']);
                }
            } else {
                // Listar todos los cursos
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
                ");
                $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar horarios para cada curso
                foreach ($cursos as &$curso) {
                    $curso['horarios'] = [];
                    if ($curso['horario_ids']) {
                        $horarioIds = explode(',', $curso['horario_ids']);
                        $horarioNombres = explode(',', $curso['horario_nombres']);
                        $horarioInicios = explode(',', $curso['horario_inicios']);
                        $horarioFines = explode(',', $curso['horario_fines']);
                        $horarioDias = explode(',', $curso['horario_dias']);

                        for ($i = 0; $i < count($horarioIds); $i++) {
                            $curso['horarios'][] = [
                                'id' => $horarioIds[$i],
                                'nombre' => $horarioNombres[$i],
                                'hora_inicio' => $horarioInicios[$i],
                                'hora_fin' => $horarioFines[$i],
                                'dias' => $horarioDias[$i]
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

                echo json_encode($cursos);
            }
            break;

        case 'POST':
            // Crear nuevo curso
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['nombre']) || !isset($data['precio'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'mensaje' => 'Datos incompletos']);
                exit;
            }

            $db->beginTransaction();
            try {
                // Insertar curso
                $stmt = $db->prepare("INSERT INTO cursos (nombre, duracion, clases_practicas, clases_teoricas, precio, descripcion) 
                                     VALUES (:nombre, :duracion, :clases_practicas, :clases_teoricas, :precio, :descripcion)");

                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':duracion', $data['duracion']);
                $stmt->bindParam(':clases_practicas', $data['clases_practicas']);
                $stmt->bindParam(':clases_teoricas', $data['clases_teoricas']);
                $stmt->bindParam(':precio', $data['precio']);
                $stmt->bindParam(':descripcion', $data['descripcion']);
                $stmt->execute();

                $cursoId = $db->lastInsertId();

                // Asociar horarios si existen
                if (isset($data['horarios']) && is_array($data['horarios'])) {
                    $insertHorario = $db->prepare("INSERT INTO curso_horario (curso_id, horario_id) VALUES (?, ?)");
                    foreach ($data['horarios'] as $horarioId) {
                        $insertHorario->execute([$cursoId, $horarioId]);
                    }
                }

                $db->commit();
                echo json_encode(['success' => true, 'mensaje' => 'Curso creado correctamente', 'id' => $cursoId]);
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'mensaje' => 'Error al crear curso: ' . $e->getMessage()]);
            }
            break;

        case 'PUT':
            // Actualizar un curso existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'mensaje' => 'ID de curso no especificado']);
                exit;
            }

            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);

            $db->beginTransaction();
            try {
                // Actualizar curso
                $stmt = $db->prepare("UPDATE cursos SET nombre = :nombre, duracion = :duracion, 
                                      clases_practicas = :clases_practicas, clases_teoricas = :clases_teoricas, 
                                      precio = :precio, descripcion = :descripcion WHERE id = :id");

                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':duracion', $data['duracion']);
                $stmt->bindParam(':clases_practicas', $data['clases_practicas']);
                $stmt->bindParam(':clases_teoricas', $data['clases_teoricas']);
                $stmt->bindParam(':precio', $data['precio']);
                $stmt->bindParam(':descripcion', $data['descripcion']);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // Eliminar asociaciones de horarios existentes
                $db->prepare("DELETE FROM curso_horario WHERE curso_id = ?")->execute([$id]);

                // Asociar nuevos horarios si existen
                if (isset($data['horarios']) && is_array($data['horarios'])) {
                    $insertHorario = $db->prepare("INSERT INTO curso_horario (curso_id, horario_id) VALUES (?, ?)");
                    foreach ($data['horarios'] as $horarioId) {
                        $insertHorario->execute([$id, $horarioId]);
                    }
                }

                $db->commit();
                echo json_encode(['success' => true, 'mensaje' => 'Curso actualizado correctamente']);
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar curso: ' . $e->getMessage()]);
            }
            break;

        case 'DELETE':
            // Eliminar un curso
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'mensaje' => 'ID de curso no especificado']);
                exit;
            }

            $id = $_GET['id'];
            $db->beginTransaction();

            try {
                // Eliminar asociaciones de horarios
                $db->prepare("DELETE FROM curso_horario WHERE curso_id = ?")->execute([$id]);

                // Eliminar el curso
                $stmt = $db->prepare("DELETE FROM cursos WHERE id = ?");
                $stmt->execute([$id]);

                $db->commit();
                echo json_encode(['success' => true, 'mensaje' => 'Curso eliminado correctamente']);
            } catch (Exception $e) {
                $db->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'mensaje' => 'Error al eliminar curso: ' . $e->getMessage()]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
}