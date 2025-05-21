<?php

namespace Backend\Services;

/**
 * Clase ArchivoHandler
 * 
 * Esta clase se encarga de manejar la subida y almacenamiento de archivos en un directorio específico.
 */
class ArchivoHandler 
{
    /**
     * @var string $directorioDestino Directorio donde se guardarán los archivos subidos.
     */
    private $directorioDestino;

    /**
     * Constructor de la clase ArchivoHandler.
     * 
     * @param string $directorioDestino Ruta del directorio donde se almacenarán los archivos.
     */
    public function __construct() 
    {
        // Ruta relativa desde este archivo
        $this->directorioDestino = __DIR__ . '/../../uploads/recibos/';
        
        // Crear el directorio si no existe
        if (!is_dir($this->directorioDestino)) {
            mkdir($this->directorioDestino, 0777, true);
        }
    }

    /**
     * Guarda un archivo subido en el directorio especificado.
     * 
     * @param array $archivo Información del archivo subido (como $_FILES['archivo']).
     * @return string|false Retorna el nombre único del archivo guardado o false si ocurre un error.
     */
    public function guardarArchivo($archivo) 
    {
        // Verificar si hay errores
        if ($archivo['error'] !== 0) {
            return false;
        }
        
        // Generar un nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $this->directorioDestino . $nombreArchivo;
        
        // Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return 'uploads/recibos/' . $nombreArchivo;
        }
        
        return false;
    }
    
    /**
     * Elimina un archivo del servidor.
     * 
     * @param string $rutaRelativa Ruta relativa del archivo a eliminar.
     * @return bool Retorna true si el archivo fue eliminado exitosamente, false en caso contrario.
     */
    public function eliminarArchivo($rutaRelativa) 
    {
        // Convertir ruta relativa a absoluta
        $rutaAbsoluta = __DIR__ . '/../../' . $rutaRelativa;
        
        // Verificar si el archivo existe y eliminarlo
        if (file_exists($rutaAbsoluta)) {
            return unlink($rutaAbsoluta);
        }
        
        return false;
    }
}