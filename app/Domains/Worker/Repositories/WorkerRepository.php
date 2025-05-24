<?php

namespace App\Domains\Worker\Repositories;

use App\Domains\Worker\Models\Worker;
use Illuminate\Database\Eloquent\Collection;

class WorkerRepository
{
    public function __construct()
    {
    }

    public function getAll(): Collection
    {
        return Worker::all();
    }

    public function getById(int $id): Worker
    {
        return Worker::findOrFail($id);
    }

    public function saveWorker(array $data): Worker
    {
        return Worker::create($data);
    }

    public function update($currentWorker): void
    {
        $currentWorker->save();
    }

    public function deleteById(int $id): void
    {
        Worker::destroy($id);
    }

    public function count(): int
    {
        return Worker::count();
    }
}
