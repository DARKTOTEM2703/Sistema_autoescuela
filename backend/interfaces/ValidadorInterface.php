<?php

namespace Backend\Interfaces;

interface ValidadorInterface
{
    /**
     * Valida los datos y devuelve un array con errores (vacío si no hay errores)
     * @param array $datos
     * @return array
     */
    public function validar(array $datos): array;
}
