<?php

namespace App\Services;

use App\Models\Review;
use App\Models\UserRequest;
use App\Repositories\ReviewRepository;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReviewService
{
    private ReviewRepository $reviewRepository;
    private CacheService $cacheService;

    private const CACHE_LIST_PREFIX = 'reviews_list';
    private const CACHE_ENTITY_PREFIX = 'reviews_entity';

    public function __construct(ReviewRepository $reviewRepository, CacheService $cacheService)
    {
        $this->reviewRepository = $reviewRepository;
        $this->cacheService = $cacheService;
    }

    public function addReview(array $data): void
    {
        $request = UserRequest::findOrFail($data['request_id']);
        if ($request->status !== 'completed')
            throw new BadRequestException("Вы не можете оставить отзыв пока ваша заявка не выполнена");

        if ($request->user_id != auth()->id()) throw new BadRequestException("Пользователь не пользовался услугой");
        if ($request->review()->exists()) throw new BadRequestException("Отзыв уже оставлен");


        $review = $this->reviewRepository->create($data);

        $this->clearCache($review->service_id, self::CACHE_LIST_PREFIX);

    }

    public function getReviewById(string $id): Review
    {
        return $this->reviewRepository->getById($id);
    }

    public function updateReview(array $data, string $id): void
    {
        $currentReview = Review::findOrFail($id);
        $request = UserRequest::findOrFail($data['request_id']);
        if ($request->user_id != auth()->id()) throw new BadRequestException("Пользователь не пользовался услугой");
        if ($currentReview->status != 'new') throw new BadRequestException("Отзыв уже утвержден");

        $this->clearCache($currentReview->category_id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $currentReview->id);

        $currentReview->fill($data);

        $this->reviewRepository->update($currentReview);
    }

    public function deleteReviewById(string $id): void
    {
        $userId = auth()->id();
        $review = Review::findOrFail($id);
        if ($review->user->id != $userId) throw new BadRequestException("Пользователь не владеет этим отзывом");

        $this->clearCache($review->category_id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $review->id);

        $this->reviewRepository->deleteById($id);
    }

    public function getReviewCount(): int
    {
        return $this->reviewRepository->count();
    }

    public function getAllReviewByUserId(int $id): Collection
    {
        return $this->reviewRepository->getAllByUserId($id);
    }

    public function getAllReviewByServiceId(int $id): Collection
    {
        return $this->reviewRepository->getAllByServiceId($id);
    }

    public function changeStatus(string $id, string $input): void
    {
        $review = $this->reviewRepository->getById($id);
        $review->status = $input;
        $review->save();

        $this->clearCache($review->category_id, self::CACHE_LIST_PREFIX);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, $review->id);

    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            self::CACHE_LIST_PREFIX,
            $categoryId,
            10,
            function () use ($categoryId) {
                return $categoryId !== null
                    ? $this->reviewRepository->getAllByServiceId($categoryId)
                    : $this->reviewRepository->findAll();
            }
        );
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

    public function clearEntityCache(string $prefix, int $id): Void
    {
        $this->cacheService->clearEntity($prefix, $id);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, $id, 10, function () use ($id) {
            return $this->reviewRepository->getById($id);
        });
    }
}
