<?php

/**
 * Este script inicializa la base de datos ejecutando el script SQL
 */

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$db_name = "autoescuela_db";

try {
    // Conexión inicial sin seleccionar base de datos
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Inicialización de la base de datos</h2>";
    echo "<p>Conexión exitosa a MySQL</p>";

    // Eliminar la base de datos si existe
    $pdo->exec("DROP DATABASE IF EXISTS $db_name");
    echo "<p>Base de datos anterior eliminada (si existía)</p>";

    // Crear la base de datos
    $pdo->exec("CREATE DATABASE $db_name");
    echo "<p>Base de datos '$db_name' creada correctamente</p>";

    // Seleccionar la base de datos
    $pdo->exec("USE $db_name");
    echo "<p>Base de datos seleccionada</p>";

    // Leer el archivo SQL (cambiado a autoescuela_db.sql)
    $sql = file_get_contents(__DIR__ . '/autoescuela_db.sql');

    if (empty($sql)) {
        throw new Exception("El archivo SQL está vacío o no se pudo leer correctamente");
    }

    // Ejecutar el script SQL
    $pdo->exec($sql);

    echo "<p>Base de datos inicializada correctamente</p>";
    echo '<p><a href="../../frontend/admin/login/index.php" style="padding: 10px 15px; background-color: #e91e63; color: white; text-decoration: none; border-radius: 4px;">Ir al login</a></p>';
    echo '<p><a href="../../frontend/landing/index.php" style="text-decoration: underline;">Volver a la página principal</a></p>';
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 15px; border: 1px solid red; margin: 15px 0;'>";
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}