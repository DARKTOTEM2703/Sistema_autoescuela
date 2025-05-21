<?php

/**
 * Función de autocarga personalizada para clases del namespace Backend
 */
spl_autoload_register(function ($class) {
    // Convertir namespace separators a directory separators
    $file = __DIR__ . '/' . str_replace('\\', '/', str_replace('Backend\\', '', $class)) . '.php';

    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
});