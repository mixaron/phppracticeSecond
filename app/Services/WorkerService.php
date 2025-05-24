<?php

namespace App\Services;

use App\Domains\Worker\Models\Worker;
use App\Domains\Worker\Repositories\WorkerRepository;
use App\Http\CacheableServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class WorkerService implements CacheableServiceInterface
{
    private WorkerRepository $workerRepository;
    private CacheService $cacheService;
    private ImageService $imageService;

    private const CACHE_LIST_PREFIX = 'workers_list';
    private const CACHE_ENTITY_PREFIX = 'workers_entity';

    public function __construct(WorkerRepository $workerRepository, CacheService $cacheService, ImageService $imageService)
    {
        $this->workerRepository = $workerRepository;
        $this->cacheService = $cacheService;
        $this->imageService = $imageService;
    }

    public function addWorker(array $data): Worker
    {
        $worker = $this->workerRepository->saveWorker($data);
        $this->clearCache(null, self::CACHE_LIST_PREFIX);
        return $worker;
    }

    public function updateWorker(array $data, int $id): Worker
    {
        $currentWorker = Worker::findOrFail($id);
        $this->clearCache(null, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $currentWorker->id);

        $currentWorker->fill($data);
        $this->workerRepository->update($currentWorker);
        return $currentWorker;
    }

    public function deleteWorkerById(int $id): void
    {
        $worker = $this->workerRepository->getById($id);
        $this->clearCache(null, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $worker->id);
        $this->imageService->deleteImages($worker);

        $this->workerRepository->deleteById($id);
    }

    public function getWorkerCount(): int
    {
        return $this->workerRepository->count();
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            self::CACHE_LIST_PREFIX,
            $categoryId,
            10,
            fn () => $this->workerRepository->getAll()
        );
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function clearEntityCache(string $prefix, int $id): void
    {
        $this->cacheService->clearEntity($prefix, $id);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id) {
            return $this->workerRepository->getById($id);
        });
    }
}
