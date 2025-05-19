<?php

namespace App\Repositories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoryRepository
{
    public function __construct()
    {
    }

    public function findAll(): Collection
    {
        return ServiceCategory::all();
    }

    public function save(array $data): void
    {
        ServiceCategory::create($data);
    }

    public function getById(string $id): ServiceCategory
    {
        return ServiceCategory::findOrFail($id);
    }

    public function update($NewsCategory): void
    {
        ServiceCategory::save($NewsCategory);
    }

    public function deleteById(string $id): void
    {
        ServiceCategory::destroy($id);
    }
}
