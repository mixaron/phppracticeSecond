<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Database\Eloquent\Collection;

class NewsService
{
    private NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function getAllNews(): Collection
    {
        return $this->newsRepository->findAllNews();
    }

    public function addNews(array $data): void
    {
        $this->newsRepository->saveNews($data);
    }

    public function getNewsById(string $id): News
    {
        return $this->newsRepository->getById($id);
    }

    public function updateNews(array $data, string $id): void
    {
        $currentNews = News::findOrFail($id);

        $currentNews->fill($data);

        $this->newsRepository->update($currentNews);
    }

    public function deleteNewsById(string $id): void
    {
        $this->newsRepository->deleteById($id);
    }

    public function getNewsCount(): int
    {
        return $this->newsRepository->count();
    }
}
