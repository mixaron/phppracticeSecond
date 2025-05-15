<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
{
    public function __construct()
    {
    }

    public function findAll(): Collection
    {
        return Review::all();
    }

    public function create(array $data): void
    {
        Review::create($data);
    }

    public function getById(string $id): Review
    {
        return Review::findOrFail($id);
    }

    public function update($currentReview): void
    {
        $currentReview->save();
    }

    public function deleteById(string $id): void
    {
        Review::destroy($id);
    }

    public function count(): int
    {
        return Review::count();
    }

    public function getAllByUserId(string $userId): Collection
    {
        return Review::where('user_id', $userId)->get();
    }

    public function getAllByServiceId(string $serviceId): Collection
    {
        return Review::where('serviceId', $serviceId)->get();
    }
}
