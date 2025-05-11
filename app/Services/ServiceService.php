<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getAllServices(): Collection
    {
        return $this->serviceRepository->findAllServices();
    }

    public function addService(array $data): void
    {
        $this->serviceRepository->saveService($data);
    }

    public function getServiceById(string $id): Service
    {
        return $this->serviceRepository->getById($id);
    }

    public function updateService(array $data, string $id): void
    {
        $currentService = Service::findOrFail($id);

        $currentService->fill($data);

        $this->serviceRepository->update($currentService);
    }

    public function deleteServiceById(string $id): void
    {
        $this->serviceRepository->deleteById($id);
    }

    public function getServiceCount(): int
    {
        return $this->serviceRepository->count();
    }
}
