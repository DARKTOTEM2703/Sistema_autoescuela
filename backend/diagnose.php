<?php
// filepath: c:\xampp\htdocs\Sistema_autoescuela\backend\diagnose.php
require_once 'autoload.php';

use Backend\Config\Database;

header('Content-Type: text/html; charset=UTF-8');

try {
    // Probar la conexión a la base de datos
    $db = Database::getInstance()->getConnection();
    echo "<p style='color:green'>✓ Conexión a la base de datos establecida correctamente</p>";

    // Verificar la tabla de horarios
    $stmt = $db->query("SHOW TABLES LIKE 'horarios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ La tabla 'horarios' existe</p>";

        // Contar registros
        $countStmt = $db->query("SELECT COUNT(*) as total FROM horarios");
        $count = $countStmt->fetchColumn();
        echo "<p>La tabla 'horarios' contiene {$count} registros</p>";

        // Mostrar estructura
        echo "<h3>Estructura de la tabla 'horarios':</h3>";
        $structStmt = $db->query("DESCRIBE horarios");
        echo "<pre>";
        print_r($structStmt->fetchAll());
        echo "</pre>";

        // Mostrar datos
        echo "<h3>Contenido de la tabla 'horarios':</h3>";
        $dataStmt = $db->query("SELECT * FROM horarios");
        echo "<pre>";
        print_r($dataStmt->fetchAll());
        echo "</pre>";
    } else {
        echo "<p style='color:red'>✗ La tabla 'horarios' NO existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}