<?php
// // Intentar guardar el archivo
namespace Backend\Model;

class Estudiante
{
    private $nombre;
    private $direccion;
    private $celular;
    private $tipoAuto;
    private $horario;
    private $reciboPath;

    public function __construct($nombre, $direccion, $celular, $tipoAuto, $horario, $reciboPath = null)
    {
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->celular = $celular;
        $this->tipoAuto = $tipoAuto;
        $this->horario = $horario;
        $this->reciboPath = $reciboPath;
    }

    // Getters
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getTipoAuto()
    {
        return $this->tipoAuto;
    }

    public function getHorario()
    {
        return $this->horario;
    }

    public function getReciboPath()
    {
        return $this->reciboPath;
    }

    // Setters
    public function setReciboPath($reciboPath)
    {
        $this->reciboPath = $reciboPath;
    }
}