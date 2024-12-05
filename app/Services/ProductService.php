<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllProducts(): Collection
    {
        return $this->repository->all();
    }

    public function getProduct(int $id): ?Model
    {
        return $this->repository->findOrFail($id);
    }

    public function createProduct(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function updateProduct(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function findProductByImage(string $filename): ?Model
    {
        return $this->repository->findByImage($filename);
    }
}
