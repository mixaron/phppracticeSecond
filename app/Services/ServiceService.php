<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    private ServiceRepository $serviceRepository;
    private ImageService $imageService;

    public function __construct(ServiceRepository $serviceRepository, ImageService $imageService)
    {
        $this->serviceRepository = $serviceRepository;
        $this->imageService = $imageService;
    }

    public function getAllServices(): Collection
    {
        return $this->serviceRepository->findAllServices();
    }

    public function addService(array $data): Service
    {
        return $this->serviceRepository->saveService($data);
    }

    public function getServiceById(string $id): Service
    {
        return $this->serviceRepository->getById($id);
    }

    public function updateService(array $data, string $id): Service
    {
        $currentService = Service::findOrFail($id);

        $currentService->fill($data);

        return $this->serviceRepository->update($currentService);
    }

    public function deleteServiceById(string $id): void
    {
        $service = $this->serviceRepository->getById($id);

        $this->imageService->deleteImages($service);

        $this->serviceRepository->deleteById($id);
    }

    public function getServiceCount(): int
    {
        return $this->serviceRepository->count();
    }

    public function getAllServicesByCategoryId(mixed $input): Collection
    {
        return Service::where('category_id', $input)->get();
    }
}
