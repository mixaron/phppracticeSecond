<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Database\Eloquent\Collection;

class NewsService
{
    private NewsRepository $newsRepository;
    private ImageService $imageService;

    public function __construct(NewsRepository $newsRepository, ImageService $imageService)
    {
        $this->newsRepository = $newsRepository;
        $this->imageService = $imageService;
    }

    public function getAllNews(): Collection
    {
        return $this->newsRepository->findAllNews();
    }

    public function addNews(array $data): News
    {
        return $this->newsRepository->saveNews($data);
    }

    public function getNewsById(string $id): News
    {
        return $this->newsRepository->getById($id);
    }

    public function updateNews(array $data, string $id): News
    {
        $currentNews = News::findOrFail($id);
        $currentNews->fill($data);
        $this->newsRepository->update($currentNews);

        return $currentNews;
    }

    public function deleteNewsById(string $id): void
    {
        $news = $this->newsRepository->getById($id);
        $this->imageService->deleteImages($news);
        $this->newsRepository->deleteById($id);
    }

    public function getNewsCount(): int
    {
        return $this->newsRepository->count();
    }

    public function getAllNewsByCategoryId(mixed $input): Collection
    {
        return News::where('category_id', $input)->get();
    }
}
