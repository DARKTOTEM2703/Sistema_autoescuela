<?php

namespace Backend\Services;

use Backend\Interfaces\RepositoryInterface;
use Backend\Interfaces\ValidadorInterface;
use Backend\Model\Estudiante;

/**
 * Servicio para manejar la lógica de negocio de los estudiantes
 * Implementa el principio de inversión de dependencias
 */
class EstudianteService
{
    private $repository;
    private $validador;
    private $archivoHandler;

    /**
     * Constructor que recibe las dependencias (inyección de dependencias)
     */
    public function __construct(
        RepositoryInterface $repository,
        ValidadorInterface $validador,
        ArchivoHandler $archivoHandler
    ) {
        $this->repository = $repository;
        $this->validador = $validador;
        $this->archivoHandler = $archivoHandler;
    }

    /**
     * Obtiene todos los estudiantes
     */
    public function getAllEstudiantes(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Obtiene un estudiante por su ID
     */
    public function getEstudianteById($id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Crea un nuevo estudiante
     */
    public function createEstudiante(array $datos, array $archivos = null)
    {
        // Validar datos
        $errores = $this->validador->validar($datos);

        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        // Procesar archivo si existe
        $reciboPath = null;
        if ($archivos && isset($archivos['recibo']) && $archivos['recibo']['error'] == 0) {
            $reciboPath = $this->archivoHandler->guardarArchivo($archivos['recibo']);

            if (!$reciboPath) {
                return [
                    'success' => false,
                    'errores' => ['Error al subir el archivo de recibo']
                ];
            }
        }

        // Mapear nombres a IDs
        $tipoAutoId = $this->repository->getTipoAutoIdByName($datos['tipo_auto']);
        $horarioId = $this->repository->getHorarioIdByName($datos['horario']);

        // Crear el estudiante
        $estudianteData = [
            'nombre' => $datos['nombre'],
            'direccion' => $datos['direccion'],
            'celular' => $datos['celular'],
            'tipo_auto_id' => $tipoAutoId,
            'horario_id' => $horarioId,
            'recibo_path' => $reciboPath
        ];

        try {
            $id = $this->repository->create($estudianteData);
            return [
                'success' => true,
                'mensaje' => 'Estudiante registrado correctamente',
                'id' => $id
            ];
        } catch (\Exception $e) {
            // Si hubo un error y se subió un archivo, eliminarlo
            if ($reciboPath) {
                $this->archivoHandler->eliminarArchivo($reciboPath);
            }
            
            return [
                'success' => false,
                'errores' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Actualiza un estudiante existente
     */
    public function updateEstudiante($id, $datos, $archivos = null)
    {
        // Validar datos
        $errores = $this->validador->validar($datos);
        if (!empty($errores)) {
            return [
                'success' => false,
                'errores' => $errores
            ];
        }

        $reciboPath = null;
        
        // Procesar archivo si se ha subido uno nuevo
        if ($archivos && isset($archivos['recibo']) && $archivos['recibo']['error'] == 0) {
            try {
                $reciboPath = $this->archivoHandler->procesarArchivo($archivos['recibo']);
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'errores' => [$e->getMessage()]
                ];
            }
        }

        // Mapear nombres a IDs
        $tipoAutoId = $this->repository->getTipoAutoIdByName($datos['tipo_auto']);
        $horarioId = $this->repository->getHorarioIdByName($datos['horario']);

        // Crear el estudiante
        $estudianteData = [
            'nombre' => $datos['nombre'],
            'direccion' => $datos['direccion'],
            'celular' => $datos['celular'],
            'tipo_auto_id' => $tipoAutoId,
            'horario_id' => $horarioId
        ];

        // Solo incluir el recibo_path si se ha subido un nuevo archivo
        if ($reciboPath) {
            $estudianteData['recibo_path'] = $reciboPath;  // ERROR: Debería ser $reciboPath
    
            // Obtener el estudiante actual para eliminar el archivo anterior
            $estudianteActual = $this->repository->getById($id);
            if ($estudianteActual && !empty($estudianteActual['recibo_path'])) {
                // Corregido el error de escritura: "reciboPath" en lugar de "recipoPath"
                $this->archivoHandler->eliminarArchivo($estudianteActual['recibo_path']);
            }
        }

        try {
            $result = $this->repository->update($id, $estudianteData);
            if ($result) {
                return [
                    'success' => true,
                    'mensaje' => 'Estudiante actualizado correctamente'
                ];
            } else {
                // Si se subió un nuevo archivo pero falló la actualización, eliminarlo
                if ($reciboPath) {
                    $this->archivoHandler->eliminarArchivo($reciboPath);
                }
                
                return [
                    'success' => false,
                    'errores' => ['No se pudo actualizar el estudiante']
                ];
            }
        } catch (\Exception $e) {
            // Si hubo un error y se subió un archivo, eliminarlo
            if ($reciboPath) {
                $this->archivoHandler->eliminarArchivo($reciboPath);
            }
            
            return [
                'success' => false,
                'errores' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Elimina un estudiante
     */
    public function deleteEstudiante($id)
    {
        try {
            // Obtener la información del estudiante para eliminar el archivo asociado
            $estudiante = $this->repository->getById($id);

            if (!$estudiante) {
                return [
                    'success' => false,
                    'mensaje' => 'No se encontró el estudiante'
                ];
            }

            // Eliminar el archivo de recibo si existe
            if (!empty($estudiante['recibo_path'])) {
                $this->archivoHandler->eliminarArchivo($estudiante['recibo_path']);
            }

            // Eliminar estudiante de la base de datos
            $result = $this->repository->delete($id);

            if ($result) {
                return [
                    'success' => true,
                    'mensaje' => 'Estudiante eliminado correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => 'No se pudo eliminar el estudiante'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene todos los tipos de auto
     */
    public function getAllTiposAuto(): array
    {
        return $this->repository->getAllTiposAuto();
    }

    /**
     * Obtiene todos los horarios
     */
    public function getAllHorarios(): array
    {
        return $this->repository->getAllHorarios();
    }
}