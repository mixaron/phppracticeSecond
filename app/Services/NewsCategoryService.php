<?php

namespace App\Services;

use App\Domains\NewsCategory\Models\NewsCategory;
use App\Domains\NewsCategory\Repositories\NewsCategoryRepository;
use App\Http\CacheableServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class NewsCategoryService implements CacheableServiceInterface
{
    private NewsCategoryRepository $newsCategoryRepository;
    private CacheService $cacheService;

    private const CACHE_LIST_PREFIX = 'news_categories';
    private const CACHE_ENTITY_PREFIX = 'news_categories_entity';

    public function __construct(NewsCategoryRepository $newsCategoryRepository, CacheService $cacheService)
    {
        $this->newsCategoryRepository = $newsCategoryRepository;
        $this->cacheService = $cacheService;
    }


    public function addNewsCategory(array $data): void
    {
        $category = $this->newsCategoryRepository->save($data);
        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);

    }

    public function updateNewsCategory(array $data, string $id): void
    {
        $category = NewsCategory::findOrFail($id);

        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $category->id);

        $category->fill($data);

        $this->newsCategoryRepository->update($category);
    }

    public function deleteNewsCategoryById(string $id): void
    {
        $category = $this->newsCategoryRepository->getById($id);

        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $category->id);

        $this->newsCategoryRepository->deleteById($id);
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            self::CACHE_LIST_PREFIX,
            null,
            10,
            function () {
                return $this->newsCategoryRepository->findAll();
            }
        );
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id){
            return $this->newsCategoryRepository->getById($id);
        });
    }

    public function clearCache(int|string $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function clearEntityCache(string $prefix, int $id): Void
    {
        $this->cacheService->clearEntity($prefix, $id);
    }
}
