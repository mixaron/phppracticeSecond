<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * @OA\Get(
     *     path="/api/service",
     *     tags={"Service"},
     *     summary="Получить список всех услуг или по категории",
     *     operationId="getAllServices",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="ID категории для фильтрации услуг",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список услуг (возможно отфильтрованный по категории)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Сообщение о результате запроса",
     *                 enum={"Список услуг", "Список услуг по категории", "Услуг по такой категории еще нет"},
     *                 example="Список услуг"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив услуг",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги"),
     *                     @OA\Property(property="title", type="string", description="Название услуги"),
     *                     @OA\Property(property="description", type="string", description="Описание услуги"),
     *                     @OA\Property(property="price", type="number", format="float", description="Стоимость услуги"),
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
        $categoryId = $request->input('category_id');
        $categoryId = is_numeric($categoryId) ? (int)$categoryId : null;
        $servicesWithCache = $this->serviceService->getListWithCache($categoryId);

        return response()->json([
            'status' => 'success',
            'message' => 'Список услуг',
            'data' => ServiceResource::collection($servicesWithCache)
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/service/{id}",
     *     tags={"Service"},
     *     summary="Получить услугу по идентификатору",
     *     operationId="getServiceById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор услуги",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Услуга",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success"),
     *             @OA\Property(property="message", type="string", example="Услуга по id"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги", example="1"),
     *                 @OA\Property(property="title", type="string", description="Название услуги", example="Создание сайта"),
     *                 @OA\Property(property="description", type="string", description="Описание услуги", example="Полноценная разработка под ключ"),
     *                 @OA\Property(property="price", type="number", format="float", description="Стоимость услуги", example=15000.00),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-21T14:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-21T14:00:00Z"),

     *                 @OA\Property(
     *                     property="reviews",
     *                     type="array",
     *                     description="Отзывы к услуге",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10, description="ID отзыва"),
     *                         @OA\Property(property="user_id", type="integer", example=5, description="ID пользователя, оставившего отзыв"),
     *                         @OA\Property(property="rating", type="integer", example=4, description="Оценка отзыва"),
     *                         @OA\Property(property="text", type="string", example="Очень доволен работой", description="Текст отзыва"),
     *                         @OA\Property(property="status", type="string", example="allowed", description="Статус модерации отзыва"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-20T10:00:00Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-20T10:00:00Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Услуга не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error"),
     *             @OA\Property(property="message", type="string", example="Услуга не найдена"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error"),
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $service = $this->serviceService->getEntityWithCache($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Услуга не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Услуга по id',
            'data' => new ServiceResource($service)
        ]);
    }
}
