<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Services\NewsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * @OA\Get(
     *     path="/api/news",
     *     tags={"News"},
     *     summary="Получить список всех новостей или по категории",
     *     operationId="getAllNews",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="ID категории для фильтрации новостей",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список новостей (возможно отфильтрованный по категории)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Сообщение о результате запроса",
     *                 enum={"Список новостей", "Список новостей по категории", "Новостей по такой категории еще нет"},
     *                 example="Список новостей"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив новостей",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор новости"),
     *                     @OA\Property(property="title", type="string", description="Заголовок новости"),
     *                     @OA\Property(property="description", type="string", description="Описание новости"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         description="Категория новости",
     *                         @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                         @OA\Property(property="title", type="string", description="Название категории")
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
        if ($request->has('category_id') && is_numeric($request->input('category_id'))) {
            $allNews = $this->newsService->getAllNewsByCategoryId($request->input('category_id'));
            if ($allNews->isEmpty()) {
                $message = 'Новостей по такой категории еще нет';
            } else {
                $message = 'Список новостей по категории';
        }
        } else {
            $allNews = $this->newsService->getAllNews();
            $message = 'Список новостей';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => NewsResource::collection($allNews)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/news/{id}",
     *     tags={"News"},
     *     summary="Получить новость по идентификатору",
     *     operationId="getNewsById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор новости",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Новость",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость по id", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные новости",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор новости"),
     *                 @OA\Property(property="title", type="string", description="Заголовок новости"),
     *                 @OA\Property(property="description", type="string", description="Описание новости"),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     description="Категория новости",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                     @OA\Property(property="title", type="string", description="Название категории")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Новость не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость не найдена", description="Сообщение об ошибке"),
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
            $news = $this->newsService->getNewsById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Новость не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Новость по id',
            'data' => new NewsResource($news)
        ]);
    }
}
