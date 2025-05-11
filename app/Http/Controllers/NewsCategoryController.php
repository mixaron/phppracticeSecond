<?php
namespace App\Http\Controllers;

use App\Http\Resources\NewsCategoryResource;
use App\Services\NewsCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class NewsCategoryController extends Controller
{
    private NewsCategoryService $newsCategoryService;

    public function __construct(NewsCategoryService $newsCategoryService)
    {
        $this->newsCategoryService = $newsCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/news",
     *     tags={"News"},
     *     summary="Получить список всех категорий новостей",
     *     operationId="getAllNewsCategories",
     *     @OA\Response(
     *         response=200,
     *         description="Список категорий новостей",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список категорий новостей", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив категорий новостей",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор категории новостей"),
     *                     @OA\Property(property="title", type="string", description="Название категории новостей"),
     *                     @OA\Property(property="description", type="string", description="Описание категории новостей"),
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
        $newsList = $this->newsCategoryService->getAllNewsCategory();

        return response()->json([
            'status' => 'success',
            'message' => 'Список категорий новостей',
            'data' => NewsCategoryResource::collection($newsList)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/news/{id}",
     *     tags={"News"},
     *     summary="Получить категорию новостей по идентификатору",
     *     operationId="getNewsCategoryById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор категории новостей",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Категория новостей",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Категория новости по id", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные категории новостей",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор категории новостей"),
     *                 @OA\Property(property="title", type="string", description="Название категории новостей"),
     *                 @OA\Property(property="description", type="string", description="Описание категории новостей"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Категория новостей не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Категория новости не найдена", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="  example="Произошла непредвиденная ошибка", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $news = $this->newsCategoryService->getNewsCategoryById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категория новости не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Категория новости по id',
            'data' => new NewsCategoryResource($news)
        ]);
    }
}
