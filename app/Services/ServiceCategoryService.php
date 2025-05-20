<?php

namespace App\Services;

use App\Http\CacheableServiceInterface;
use App\Models\ServiceCategory;
use App\Repositories\ServiceCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoryService implements CacheableServiceInterface
{
    private ServiceCategoryRepository $serviceCategoryRepository;
    private CacheService $cacheService;

    private const CACHE_LIST_PREFIX = 'service_categories';
    private const CACHE_ENTITY_PREFIX = 'service_categories_entity';

    public function __construct(ServiceCategoryRepository $serviceCategoryRepository, CacheService $cacheService)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepository;
        $this->cacheService = $cacheService;
    }

    public function addServiceCategory(array $data): void
    {
        $category = $this->serviceCategoryRepository->save($data);
        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);

    }

    public function updateServiceCategory(array $data, string $id): void
    {
        $category = ServiceCategory::findOrFail($id);
        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $category->id);

        $category->fill($data);

        $this->serviceCategoryRepository->update($category);
    }

    public function deleteServiceCategoryById(string $id): void
    {
        $category = $this->serviceCategoryRepository->getById($id);

        $this->clearCache($category->id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $category->id);

        $this->serviceCategoryRepository->deleteById($id);
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            self::CACHE_LIST_PREFIX,
            null,
            10,
            function () {
                return $this->serviceCategoryRepository->findAll();
            }
        );
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id){
            return $this->serviceCategoryRepository->getById($id);
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
