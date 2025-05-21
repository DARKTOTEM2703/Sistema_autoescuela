<?php

namespace Backend\Config;

use PDO;
use PDOException;

/**
 * Clase Database que se encarga de la conexión con la base de datos
 * Implementa el patrón Singleton para garantizar una única instancia de conexión
 */
class Database
{
    private static $instance = null;
    private $connection;

    // Configuración de la base de datos
    private $host = "localhost";
    private $db_name = "autoescuela_db";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";

    /**
     * Constructor privado para prevenir instanciación directa
     */
    private function __construct()
    {
        try {
            // Primero intentamos conectar sin especificar base de datos por si no existe
            $pdo = new PDO("mysql:host={$this->host}", $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verificamos si existe la base de datos
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->db_name}'");
            if (!$stmt->fetch()) {
                // Si no existe, mostramos mensaje amigable
                throw new PDOException("La base de datos '{$this->db_name}' no existe. Por favor ejecute el script de inicialización.");
            }
            
            // Si existe, nos conectamos normalmente
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Registrar el error pero mostrando un mensaje más amigable
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new PDOException("Error al conectar con la base de datos. Verifique que el servicio MySQL esté activo y que la base de datos exista.");
        }
    }

    /**
     * Método para obtener la instancia de la base de datos (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prevenir la clonación del objeto
     */
    private function __clone() {}
}