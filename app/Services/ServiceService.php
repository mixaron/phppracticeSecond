<?php

namespace App\Services;

use App\Http\CacheableServiceInterface;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;

class ServiceService implements CacheableServiceInterface
{
    private ServiceRepository $serviceRepository;
    private ImageService $imageService;
    private CacheService $cacheService;

    private const CACHE_LIST_PREFIX = 'services_list';
    private const CACHE_ENTITY_PREFIX = 'services_entity';

    public function __construct(ServiceRepository $serviceRepository, ImageService $imageService, CacheService $cacheService)
    {
        $this->serviceRepository = $serviceRepository;
        $this->imageService = $imageService;
        $this->cacheService = $cacheService;
    }

    public function addService(array $data): Service
    {
        return $this->serviceRepository->saveService($data);
    }

    public function updateService(array $data, string $id): Service
    {
        $currentService = Service::findOrFail($id);
        $this->clearCache($data['category_id'] ?? null, self::CACHE_LIST_PREFIX);
        $this->clearCache(null, "services_entity_{$currentService->id}");

        $currentService->fill($data);

        return $this->serviceRepository->update($currentService);
    }

    public function deleteServiceById(string $id): void
    {
        $service = $this->serviceRepository->getById($id);

        $this->clearCache($data['category_id'] ?? null, self::CACHE_LIST_PREFIX);
        $this->clearCache(null, "services_entity_{$service->id}");

        $this->imageService->deleteImages($service);

        $this->serviceRepository->deleteById($id);
    }

    public function getServiceCount(): int
    {
        return $this->serviceRepository->count();
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            'services_list',
            $categoryId,
            10,
            function () use ($categoryId) {
                return $categoryId
                    ? $this->serviceRepository->getAllByCategoryId($categoryId)
                    : $this->serviceRepository->findAllServices();
            }
        );
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id) {
            return $this->serviceRepository->getById($id);
        });
    }
}
