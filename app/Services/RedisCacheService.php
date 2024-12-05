<?php

namespace App\Services;

use App\Services\Interfaces\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

class RedisCacheService implements CacheServiceInterface
{
    private const DEFAULT_TTL = 3600;

    public function remember(string $key, int $ttl = self::DEFAULT_TTL, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function tags(array $tags): self
    {
        Cache::tags($tags);
        return $this;
    }
}
