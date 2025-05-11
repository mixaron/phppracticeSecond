<?php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository
{
    public function __construct()
    {
    }

    public function findAllServices(): Collection
    {
        return Service::all();
    }

    public function saveService(array $data): void
    {
        Service::create($data);
    }

    public function getById(string $id): Service
    {
        return Service::findOrFail($id);
    }

    public function update($currentService): void
    {
        $currentService->save();
    }

    public function deleteById(string $id): void
    {
        Service::destroy($id);
    }

    public function count(): int
    {
        return Service::count();
    }

}
