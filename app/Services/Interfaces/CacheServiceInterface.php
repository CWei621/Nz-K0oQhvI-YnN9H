<?php

namespace App\Services\Interfaces;

interface CacheServiceInterface
{
    public function remember(string $key, int $ttl, callable $callback);
    public function forget(string $key): void;
    public function tags(array $tags): self;
}
