<?php

namespace App\Http\Controllers;

use App\Domains\Review\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     tags={"Reviews"},
     *     summary="Получить список всех отзывов или отзывы пользователя",
     *     operationId="getAllReviews",
     *     @OA\Response(
     *         response=200,
     *         description="Список отзывов",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список отзывов", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив отзывов",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор отзыва"),
     *                     @OA\Property(property="description", type="string", description="Текст отзыва"),
     *                     @OA\Property(property="estimation", type="integer", description="Оценка (от 0 до 5)", example=5),
     *                     @OA\Property(property="status", type="string", description="Статус отзыва", example="on_moderation"),
     *                     @OA\Property(
     *                         property="request",
     *                         type="object",
     *                         description="Информация о заявке",
     *                         @OA\Property(property="id", type="string", description="ID заявки"),
     *                         @OA\Property(property="user_id", type="string", description="Название заявки"),
     *                         @OA\Property(property="service_id", type="string", description="Название услуги")
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $allReviews = $this->reviewService->getListWithCache(null);
        $message = 'Список отзывов';

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => ReviewResource::collection($allReviews)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Получить отзыв по идентификатору",
     *     operationId="getReviewById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор отзыва",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Отзыв",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Отзыв по id", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные отзыва",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор отзыва"),
     *                 @OA\Property(property="description", type="string", description="Текст отзыва"),
     *                 @OA\Property(property="estimation", type="integer", description="Оценка (от 0 до 5)", example=5),
     *                 @OA\Property(property="status", type="string", description="Статус отзыва", example="on_moderation"),
     *                 @OA\Property(
     *                     property="request",
     *                     type="object",
     *                     description="Информация о заявке",
     *                     @OA\Property(property="id", type="string", description="ID заявки"),
     *                     @OA\Property(property="user_id", type="string", description="Название заявки"),
     *                     @OA\Property(property="service_id", type="string", description="Название услуги")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Отзыв не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Отзыв не найден", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $review = $this->reviewService->getEntityWithCache($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отзыв не найден',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Отзыв по id',
            'data' => new ReviewResource($review)
        ]);
    }
}
