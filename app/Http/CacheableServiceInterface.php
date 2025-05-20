<?php

namespace App\Http;

use Illuminate\Database\Eloquent\Collection;

interface CacheableServiceInterface
{
    public function getListWithCache(?int $categoryId): Collection;
    public function getEntityWithCache(int $id): mixed;
    public function clearCache(string|int $categoryId, string $prefix): void;
    public function clearEntityCache(string $prefix, int $id);
}
