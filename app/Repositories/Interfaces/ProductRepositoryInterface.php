<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findOrFail(int $id);
    public function findByImage(string $filename);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
