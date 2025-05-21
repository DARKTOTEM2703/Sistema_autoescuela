<?php

namespace Backend\Repositories;

use Backend\Config\Database;
use Backend\Interfaces\RepositoryInterface;
use Backend\Model\Estudiante;
use PDO;
use PDOException;

/**
 * Repositorio para manejar estudiantes en la base de datos
 * Implementa la interfaz RepositoryInterface
 */
class EstudianteRepository implements RepositoryInterface
{
    private $db;
    
    public function __construct()
    {
        // Obtener la conexión a la base de datos usando el Singleton
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene todos los estudiantes de la base de datos
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT 
                e.*, 
                t.nombre AS tipo_auto_nombre, 
                h.nombre AS horario_nombre,
                h.hora_inicio,
                h.hora_fin,
                h.dias
            FROM estudiantes e
            JOIN tipos_auto t ON e.tipo_auto_id = t.id
            JOIN horarios h ON e.horario_id = h.id
            ORDER BY e.fecha_registro DESC
        ");
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un estudiante por su ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.*, 
                t.nombre AS tipo_auto_nombre, 
                h.nombre AS horario_nombre,
                h.hora_inicio,
                h.hora_fin,
                h.dias
            FROM estudiantes e
            JOIN tipos_auto t ON e.tipo_auto_id = t.id
            JOIN horarios h ON e.horario_id = h.id
            WHERE e.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Crea un nuevo estudiante en la base de datos
     */
    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO estudiantes (nombre, direccion, celular, tipo_auto_id, horario_id, recibo_path)
                VALUES (:nombre, :direccion, :celular, :tipo_auto_id, :horario_id, :recibo_path)
            ");
            
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $data['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':celular', $data['celular'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_auto_id', $data['tipo_auto_id'], PDO::PARAM_INT);
            $stmt->bindParam(':horario_id', $data['horario_id'], PDO::PARAM_INT);
            $stmt->bindParam(':recibo_path', $data['recibo_path'], PDO::PARAM_STR);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Error al crear estudiante: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un estudiante existente
     */
    public function update($id, array $data)
    {
        try {
            $sql = "UPDATE estudiantes SET 
                    nombre = :nombre, 
                    direccion = :direccion, 
                    celular = :celular, 
                    tipo_auto_id = :tipo_auto_id, 
                    horario_id = :horario_id";
            
            // Si se proporciona un nuevo recibo, incluirlo en la actualización
            if (!empty($data['recibo_path'])) {
                $sql .= ", recibo_path = :recibo_path";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $data['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':celular', $data['celular'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_auto_id', $data['tipo_auto_id'], PDO::PARAM_INT);
            $stmt->bindParam(':horario_id', $data['horario_id'], PDO::PARAM_INT);
            
            if (!empty($data['recibo_path'])) {
                $stmt->bindParam(':recibo_path', $data['recibo_path'], PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar estudiante: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina un estudiante por su ID
     */
    public function delete($id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM estudiantes WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar estudiante: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el tipo de auto por su nombre
     */
    public function getTipoAutoIdByName($nombre)
    {
        $stmt = $this->db->prepare("SELECT id FROM tipos_auto WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    /**
     * Obtiene el horario por su nombre
     */
    public function getHorarioIdByName($nombre)
    {
        $stmt = $this->db->prepare("SELECT id FROM horarios WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    /**
     * Obtiene todos los tipos de auto
     */
    public function getAllTiposAuto(): array
    {
        $stmt = $this->db->query("SELECT * FROM tipos_auto");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene todos los horarios
     */
    public function getAllHorarios(): array
    {
        $stmt = $this->db->query("SELECT * FROM horarios");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un horario por su ID
     */
    public function getHorarioById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM horarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo horario
     */
    public function createHorario(array $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO horarios (nombre, hora_inicio, hora_fin, dias, capacidad)
                VALUES (:nombre, :hora_inicio, :hora_fin, :dias, :capacidad)
            ");
            
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio', $data['hora_inicio'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin', $data['hora_fin'], PDO::PARAM_STR);
            $stmt->bindParam(':dias', $data['dias'], PDO::PARAM_STR);
            $stmt->bindParam(':capacidad', $data['capacidad'], PDO::PARAM_INT);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Error al crear horario: " . $e->getMessage());
        }
    }

    /**
     * Actualiza un horario existente
     */
    public function updateHorario($id, array $data)
    {
        try {
            $sql = "UPDATE horarios SET 
                    nombre = :nombre, 
                    hora_inicio = :hora_inicio, 
                    hora_fin = :hora_fin, 
                    dias = :dias";
            
            if (isset($data['capacidad'])) {
                $sql .= ", capacidad = :capacidad";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_inicio', $data['hora_inicio'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_fin', $data['hora_fin'], PDO::PARAM_STR);
            $stmt->bindParam(':dias', $data['dias'], PDO::PARAM_STR);
            
            if (isset($data['capacidad'])) {
                $stmt->bindParam(':capacidad', $data['capacidad'], PDO::PARAM_INT);
            }
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar horario: " . $e->getMessage());
        }
    }

    /**
     * Elimina un horario por su ID
     */
    public function deleteHorario($id)
    {
        try {
            // Primero verificar si hay estudiantes que usan este horario
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM estudiantes WHERE horario_id = :id");
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() > 0) {
                throw new \Exception("No se puede eliminar el horario porque hay estudiantes que lo están utilizando");
            }
            
            $stmt = $this->db->prepare("DELETE FROM horarios WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar horario: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los estudiantes con sus relaciones (tipo de auto y horario)
     */
    public function getAllWithRelations()
    {
        try {
            $sql = "SELECT e.*, 
                    ta.nombre AS tipo_auto_nombre,
                    h.nombre AS horario_nombre,
                    h.hora_inicio,
                    h.hora_fin
                    FROM estudiantes e
                    LEFT JOIN tipos_auto ta ON e.tipo_auto_id = ta.id
                    LEFT JOIN horarios h ON e.horario_id = h.id
                    ORDER BY e.id DESC";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener estudiantes con relaciones: " . $e->getMessage());
        }
    }
}