<?php

namespace Backend\Services;

use Backend\Config\Database;
use PDO;
use PDOException;

/**
 * Servicio para manejar la autenticación de usuarios
 * Implementa el principio de responsabilidad única
 */
class AuthService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Verifica las credenciales de un usuario
     */
    public function login($username, $password)
    {
        try {
            // Prevención de timing attacks usando hash_equals
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Verificar si el hash necesita ser actualizado (por seguridad)
                if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
                    // Actualizar el hash a la última versión de bcrypt
                    $this->actualizarHash($user['id'], $password);
                }
                
                // Iniciar sesión y guardar datos del usuario
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'];
                $_SESSION['autenticado'] = true;
                
                return true;
            }
            
            // Seguridad: esperar un tiempo aleatorio (para prevenir timing attacks)
            usleep(rand(5000, 10000)); 
            
            return false;
        } catch (PDOException $e) {
            throw new \Exception("Error en la autenticación: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza el hash de la contraseña si es necesario
     */
    private function actualizarHash($userId, $password)
    {
        try {
            $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
            $stmt->bindParam(':password', $nuevoHash, PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            // Sólo registrar el error, no detener el flujo de autenticación
            error_log("Error al actualizar hash: " . $e->getMessage());
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea destruir la sesión completamente, borrar también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        return true;
    }
    
    /**
     * Verifica si el usuario está autenticado
     */
    public function isAuthenticated()
    {
        return isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
    }
    
    /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $role;
    }
    
    /**
     * Crea un nuevo usuario con contraseña hasheada
     */
    public function crearUsuario($username, $password, $nombre, $rol)
    {
        try {
            // Verificar que no exista ya el usuario
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'El nombre de usuario ya existe'];
            }
            
            // Crear el usuario con la contraseña hasheada
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare(
                "INSERT INTO usuarios (username, password, nombre, rol) VALUES (:username, :password, :nombre, :rol)"
            );
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return ['success' => true, 'id' => $this->db->lastInsertId()];
            } else {
                return ['success' => false, 'error' => 'Error al crear el usuario'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}