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

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getAllReviews(): Collection
    {
        return $this->reviewRepository->findAll();
    }

    public function addReview(array $data): void
    {
        $request = UserRequest::findOrFail($data['request_id']);
        if ($request->status !== 'completed')
            throw new BadRequestException("Вы не можете оставить отзыв пока ваша заявка не выполнена");

        if ($request->user_id != auth()->id()) throw new BadRequestException("Пользователь не пользовался услугой");
        if ($request->review()->exists()) throw new BadRequestException("Отзыв уже оставлен");
        $this->reviewRepository->create($data);
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
        $currentReview->fill($data);

        $this->reviewRepository->update($currentReview);
    }

    public function deleteReviewById(string $id): void
    {
        $userId = auth()->id();
        $review = Review::findOrFail($id);
        if ($review->user->id != $userId) throw new BadRequestException("Пользователь не владеет этим отзывом");
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
    }
}
