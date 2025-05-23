<?php

namespace App\Domains\News\Repositories;

use App\Domains\News\Models\News;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository
{
    public function __construct()
    {
    }

    public function findAllNews(): Collection
    {
        return News::all();
    }

    public function saveNews(array $data): News
    {
        return News::create($data);
    }

    public function getById(string $id): News
    {
        return News::findOrFail($id);
    }

    public function update($currentNews): void
    {
        $currentNews->save();
    }

    public function deleteById(string $id): void
    {
        News::destroy($id);
    }

    public function count(): int
    {
        return News::count();
    }

    public function findAllNewsByCategoryId(int $categoryId): Collection
    {
        return News::where('category_id', $categoryId)->get();
    }

}
