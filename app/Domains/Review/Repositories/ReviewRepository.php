<?php

namespace App\Domains\Review\Repositories;

use App\Domains\Review\Models\Review;
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

    public function create(array $data): Review
    {
        return Review::create($data);
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
        return Review::whereIn('request_id', function ($query) use ($userId) {
            $query->select('id')
                ->from('requests')
                ->where('user_id', $userId);
        })->get();
    }


    public function getAllByServiceId(string $serviceId): Collection
    {
        return Review::where('serviceId', $serviceId)->get();
    }
}
