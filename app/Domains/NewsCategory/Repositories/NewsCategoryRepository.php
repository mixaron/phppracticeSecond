<?php

namespace App\Domains\NewsCategory\Repositories;

use App\Domains\NewsCategory\Models\NewsCategory;
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

    public function save(array $data): NewsCategory
    {
        return NewsCategory::create($data);
    }

    public function getById(string $id): NewsCategory
    {
        return NewsCategory::findOrFail($id);
    }

    public function update($newsCategory): void
    {
        $newsCategory->save();
    }

    public function deleteById(string $id): void
    {
        NewsCategory::destroy($id);
    }
}
