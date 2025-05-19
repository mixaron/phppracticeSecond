<?php

namespace App\Services;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function rememberByCategory(string $prefix,?int $categoryId, int $minutes, Closure $callback): Collection
    {
        $cacheKey = $categoryId ? "{$prefix}_category_{$categoryId}" : "{$prefix}_all";

        return Cache::remember($cacheKey, now()->addMinutes($minutes), $callback);
    }

    public function rememberById(string $prefix, int $id, int $minutes, Closure $callback): mixed
    {
        $cacheKey = "{$prefix}_{$id}_all";

        return Cache::remember($cacheKey, now()->addMinutes($minutes), $callback);
    }

    public function clearByCategory(string $prefix, mixed $categoryId): void
    {
        Cache::forget("{$prefix}_all");
        if ($categoryId != null) Cache::forget("{$prefix}_category_{$categoryId}");
    }
}

