<?php

namespace App\Services;

use App\Models\NewsCategory;
use App\Repositories\NewsCategoryRepository;
use Illuminate\Support\Collection;

class NewsCategoryService
{
    private NewsCategoryRepository $newsCategoryRepository;

    public function __construct(NewsCategoryRepository $newsCategoryRepository)
    {
        $this->newsCategoryRepository = $newsCategoryRepository;
    }

    public function getAllNewsCategory(): Collection
    {
        return $this->newsCategoryRepository->findAll();
    }

    public function addNewsCategory(array $data): void
    {
        $this->newsCategoryRepository->save($data);
    }

    public function getNewsCategoryById(string $id): NewsCategory
    {
        return $this->newsCategoryRepository->getById($id);
    }

    public function updateNewsCategory(array $data, string $id): void
    {
        $NewsCategory = NewsCategory::findOrFail($id);

        $NewsCategory->fill($data);

        $this->newsCategoryRepository->update($NewsCategory);
    }

    public function deleteNewsCategoryById(string $id): void
    {
        $this->newsCategoryRepository->deleteById($id);
    }

}
