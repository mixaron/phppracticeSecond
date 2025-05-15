<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * @OA\Get(
     *     path="/api/user/reviews",
     *     tags={"Reviews"},
     *     summary="Получить список всех отзывов или отзывы пользователя",
     *     operationId="getAllReviews",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Фильтрация отзывов по ID пользователя (если true, возвращаются отзывы авторизованного пользователя)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
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
    public function index(Request $request)
    {
        if ($request->query('user_id') === true) {
            $allReviews = $this->reviewService->getAllReviewByUserId(auth()->id());
            $message = 'Список отзывов пользователя';
        } else {
            $allReviews = $this->reviewService->getAllReviews();
            $message = 'Список отзывов';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => ReviewResource::collection($allReviews)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/reviews/{id}",
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
            $review = $this->reviewService->getReviewById($id);
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

    /**
     * @OA\Post(
     *     path="/api/user/reviews",
     *     tags={"Reviews"},
     *     summary="Создать новый отзыв",
     *     operationId="createReview",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"description", "estimation", "request_id"},
     *             @OA\Property(property="description", type="string", description="Текст отзыва", example="Отличный сервис!"),
     *             @OA\Property(property="estimation", type="integer", description="Оценка (от 0 до 5)", example=5),
     *             @OA\Property(property="request_id", type="string", description="Идентификатор связанной заявки", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Отзыв создан и отправлен на модерацию",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"created"}, example="created", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Отзыв создан и отправлен на модерацию", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после создания)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Доступ запрещен",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Доступ запрещен", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заявка не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка не найдена", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Ошибка валидации", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="object", description="Детали ошибки валидации")
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
    public function store(ReviewRequest $request)
    {
        $status = 'created';
        $message = 'Отзыв создан и отправлен на модерацию';
        $code = 200;

        try {
            $this->reviewService->addReview([
                'description' => $request->input('description'),
                'estimation' => $request->input('estimation'),
                'request_id' => $request->input('request_id'),
                'status' => 'on_moderation'
            ]);
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Заявка не найдена';
            $code = 404;
        } catch (BadRequestException $e) {
            $status = 'error';
            $message = $e->getMessage();
            $code = 403;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * @OA\Patch(
     *     path="/api/user/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Обновить отзыв по идентификатору",
     *     operationId="updateReview",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор отзыва",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="description", type="string", description="Текст отзыва", example="Обновленный отзыв"),
     *             @OA\Property(property="estimation", type="integer", description="Оценка (от 0 до 5)", example=4),
     *             @OA\Property(property="request_id", type="string", description="Идентификатор связанной заявки", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Отзыв обновлен",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Отзыв обновлен", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после обновления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Доступ запрещен",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Доступ запрещен", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
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
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Ошибка валидации", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="object", description="Детали ошибки валидации")
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
    public function update(ReviewRequest $request, string $id)
    {
        $status = 'updated';
        $message = 'Отзыв обновлен';
        $code = 200;
        try {
            $this->reviewService
                ->updateReview($request->only(['description', 'estimation', 'request_id']), $id);
        } catch (BadRequestException $e) {
            $status = 'error';
            $message = $e->getMessage();
            $code = 403;
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Отзыв не найден';
            $code = 404;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/reviews/{id}",
     *     tags={"Reviews"},
     *     summary="Удалить отзыв по идентификатору",
     *     operationId="deleteReview",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор отзыва",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Отзыв удален",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Отзыв удален", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после удаления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Доступ запрещен",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Доступ запрещен", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
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
    public function destroy(string $id)
    {
        $status = 'deleted';
        $message = 'Отзыв удален';
        $code = 200;

        try {
            $this->reviewService->deleteReviewById($id);
        } catch (BadRequestException $e) {
            $status = 'error';
            $message = $e->getMessage();
            $code = 403;
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Отзыв не найден';
            $code = 404;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null
        ], $code);
    }
}
