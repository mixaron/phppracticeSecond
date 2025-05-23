<?php

namespace App\Services;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Repositories\ServiceRepository;
use App\Http\CacheableServiceInterface;
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
        $service =  $this->serviceRepository->saveService($data);
        $this->clearCache($service->category_id, self::CACHE_LIST_PREFIX);
        return $service;
    }

    public function updateService(array $data, string $id): Service
    {
        $currentService = Service::findOrFail($id);
        $this->clearCache($currentService->category_id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $currentService->id);


        $currentService->fill($data);

        $this->serviceRepository->update($currentService);
        return $currentService;
    }

    public function deleteServiceById(string $id): void
    {
        $service = $this->serviceRepository->getById($id);

        $this->clearCache($service->category_id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $service->id);

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
            self::CACHE_LIST_PREFIX,
            $categoryId,
            10,
            function () use ($categoryId) {
                return $categoryId !== null
                    ? $this->serviceRepository->getAllByCategoryId($categoryId)
                    : $this->serviceRepository->findAllServices();
            }
        );
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function clearEntityCache(string $prefix, int $id): Void
    {
        $this->cacheService->clearEntity($prefix, $id);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id) {
            return $this->serviceRepository->getById($id);
        });
    }
}
