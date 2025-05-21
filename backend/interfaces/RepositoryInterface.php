<?php

namespace Backend\Interfaces;

/**
 * Interface para los repositorios
 * Define los métodos básicos que todo repositorio debe implementar
 */
interface RepositoryInterface
{
    public function getAll(): array;
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id): bool;
}