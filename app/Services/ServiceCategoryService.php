<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Repositories\ServiceCategoryRepository;
use Illuminate\Support\Collection;

class ServiceCategoryService
{
    private ServiceCategoryRepository $serviceCategoryRepository;

    public function __construct(ServiceCategoryRepository $serviceCategoryRepository)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepository;
    }

    public function getAllServiceCategory(): Collection
    {
        return $this->serviceCategoryRepository->findAll();
    }

    public function addServiceCategory(array $data): void
    {
        $this->serviceCategoryRepository->save($data);
    }

    public function getServiceCategoryById(string $id): ServiceCategory
    {
        return $this->serviceCategoryRepository->getById($id);
    }

    public function updateServiceCategory(array $data, string $id): void
    {
        $NewsCategory = ServiceCategory::findOrFail($id);

        $NewsCategory->fill($data);

        $this->serviceCategoryRepository->update($NewsCategory);
    }

    public function deleteServiceCategoryById(string $id): void
    {
        $this->serviceCategoryRepository->deleteById($id);
    }

}
