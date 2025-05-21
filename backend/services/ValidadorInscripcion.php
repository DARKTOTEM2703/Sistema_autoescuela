<?php

namespace Backend\Services;

use Backend\Interfaces\ValidadorInterface;

class ValidadorInscripcion implements ValidadorInterface
{

    public function validar(array $datos): array
    {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($datos['direccion'])) {
            $errores[] = "La dirección es obligatoria";
        }

        if (empty($datos['celular'])) {
            $errores[] = "El número de celular es obligatorio";
        }

        if (empty($datos['tipo_auto'])) {
            $errores[] = "Debe seleccionar un tipo de auto";
        }

        if (empty($datos['horario'])) {
            $errores[] = "Debe seleccionar un horario preferido";
        }

        // Validación de archivo adjunto
        if (!isset($_FILES['recibo']) || $_FILES['recibo']['error'] != 0) {
            $errores[] = "Debe adjuntar un comprobante de pago";
        } else {
            $archivo = $_FILES['recibo'];
            $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensiones_permitidas)) {
                $errores[] = "El archivo debe ser PDF, JPG, JPEG o PNG";
            }
        }

        return $errores;
    }
}