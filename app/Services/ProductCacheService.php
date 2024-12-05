<?php

namespace App\Services;

use App\Services\Interfaces\CacheServiceInterface;

class ProductCacheService
{
    private const CACHE_TTL = 3600;

    private $cache;

    public function __construct(CacheServiceInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getAllProducts(callable $callback)
    {
        return $this->cache->remember('products.all', self::CACHE_TTL, $callback);
    }

    public function getProduct(int $id, callable $callback)
    {
        return $this->cache->remember("products.{$id}", self::CACHE_TTL, $callback);
    }

    public function getProductImage(string $filename, callable $callback)
    {
        return $this->cache->remember("product.image.{$filename}", self::CACHE_TTL, $callback);
    }

    public function clearProductCaches(?int $id = null): void
    {
        $this->cache->forget('products.all');

        if ($id) {
            $this->cache->forget("products.{$id}");
        }
    }

    public function clearProductImageCache(string $filename): void
    {
        $this->cache->forget("product.image.{$filename}");
    }
}
