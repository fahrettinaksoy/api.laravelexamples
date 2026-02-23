<?php

declare(strict_types=1);

namespace App\Traits;

use Closure;
use JsonException;
use Illuminate\Support\Facades\Cache;

trait HasCacheStrategy
{
    protected string $cacheTag;

    protected int $cacheTtl;

    protected function cached(string $method, mixed $data, Closure $callback): mixed
    {
        $cacheKey = $this->buildCacheKey($method, $data);

        return Cache::tags([$this->cacheTag])->remember($cacheKey, $this->cacheTtl, $callback);
    }

    protected function invalidating(Closure $callback): mixed
    {
        $result = $callback();
        Cache::tags([$this->cacheTag])->flush();

        return $result;
    }

    protected function buildCacheKey(string $method, mixed $data): string
    {
        $normalized = $this->normalizeForCache($data);

        try {
            $hash = hash('xxh128', json_encode($normalized, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            $hash = hash('xxh128', serialize($normalized));
        }

        return "{$this->cacheTag}.{$method}.{$hash}";
    }

    protected function normalizeForCache(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        $isSequential = array_is_list($data);

        $normalized = array_map(
            fn (mixed $value) => is_array($value) ? $this->normalizeForCache($value) : $value,
            $data,
        );

        if ($isSequential) {
            sort($normalized);
        } else {
            ksort($normalized);
        }

        return $normalized;
    }
}
