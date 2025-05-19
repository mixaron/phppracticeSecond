<?php

namespace App\Services;

use App\Http\CacheableServiceInterface;
use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Database\Eloquent\Collection;

class NewsService implements CacheableServiceInterface
{
    private NewsRepository $newsRepository;
    private ImageService $imageService;
    private CacheService $cacheService;
    private const CACHE_LIST_PREFIX = 'news_list';
    private const CACHE_ENTITY_PREFIX = 'news_entity';


    public function __construct(NewsRepository $newsRepository, ImageService $imageService, CacheService $cacheService)
    {
        $this->newsRepository = $newsRepository;
        $this->imageService = $imageService;
        $this->cacheService = $cacheService;
    }

    public function addNews(array $data): News
    {
        $this->clearCache($data['category_id'] ?? null, self::CACHE_LIST_PREFIX);
        return $this->newsRepository->saveNews($data);
    }

    public function updateNews(array $data, string $id): News
    {
        $currentNews = News::findOrFail($id);
        $currentNews->fill($data);
        $this->newsRepository->update($currentNews);

        $this->clearCache($data['category_id'] ?? null, self::CACHE_LIST_PREFIX);
        $this->clearCache(null, "news_entity_{$currentNews->id}");


        return $currentNews;
    }

    public function deleteNewsById(string $id): void
    {
        $news = $this->newsRepository->getById($id);

        $this->clearCache($news->category_id, self::CACHE_LIST_PREFIX);
        $this->clearCache(null, "news_entity_{$news->id}");

        $this->imageService->deleteImages($news);
        $this->newsRepository->deleteById($id);
    }

    public function getNewsCount(): int
    {
        return $this->newsRepository->count();
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            'news_list',
            $categoryId,
            10,
            function () use ($categoryId) {
                return $categoryId
                    ? $this->newsRepository->findAllNewsByCategoryId($categoryId)
                    : $this->newsRepository->findAllNews();
            }
        );
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id) {
            return $this->newsRepository->getById($id);
        });
    }
}
