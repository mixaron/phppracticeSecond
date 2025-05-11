<?php

namespace App\Repositories;

use App\Models\NewsCategory;
use Illuminate\Support\Collection;

class NewsCategoryRepository
{
    public function __construct()
    {
    }

    public function findAll(): Collection
    {
        return NewsCategory::all();
    }

    public function save(array $data): void
    {
        NewsCategory::create($data);
    }

    public function getById(string $id): NewsCategory
    {
        return NewsCategory::findOrFail($id);
    }

    public function update($NewsCategory): void
    {
        NewsCategory::save($NewsCategory);
    }

    public function deleteById(string $id): void
    {
        NewsCategory::destroy($id);
    }
}
