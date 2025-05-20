<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceCategoryResource;
use App\Services\ServiceCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceCategoryController extends Controller
{
    private ServiceCategoryService $serviceCategoryService;

    public function __construct(ServiceCategoryService $serviceCategoryService)
    {
        $this->serviceCategoryService = $serviceCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/service-categories",
     *     tags={"Service-Categories"},
     *     summary="Получить список всех категорий услуг",
     *     operationId="getAllServiceCategories",
     *     @OA\Response(
     *         response=200,
     *         description="Список категорий услуг",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список категорий новостей", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив категорий услуг",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор категории услуг"),
     *                     @OA\Property(property="title", type="string", description="Название категории услуг"),
     *                     @OA\Property(property="description", type="string", description="Описание категории услуг"),
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
        $categories = $this->serviceCategoryService->getListWithCache(null);

        return response()->json([
            'status' => 'success',
            'message' => 'Список категорий услуг',
            'data' => ServiceCategoryResource::collection($categories)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/service-categories/{id}",
     *     tags={"Service-Categories"},
     *     summary="Получить категорию услуг по идентификатору",
     *     operationId="getServiceCategoryById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор категории услуг",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Категория услуг",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Категория новости по id", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные категории услуг",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор категории услуг"),
     *                 @OA\Property(property="title", type="string", description="Название категории услуг"),
     *                 @OA\Property(property="description", type="string", description="Описание категории услуг"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Категория услуги не найдена",
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
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $news = $this->serviceCategoryService->getEntityWithCache($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категория услуг не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Категория услуг по id',
            'data' => new ServiceCategoryResource($news)
        ]);
    }
}
