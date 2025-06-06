<?php

namespace App\Http\Controllers\admin;

use App\Domains\Service\Resources\ServiceResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ImageService;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    private ServiceService $serviceService;
    private ImageService $imageService;

    public function __construct(ServiceService $serviceService, \App\Services\ImageService $imageService)
    {
        $this->serviceService = $serviceService;
        $this->imageService = $imageService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/services",
     *     tags={"Admin/Services"},
     *     summary="Получить список всех услуг",
     *     operationId="getAllServiceAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список услуг",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список услуг", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив услуг",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги"),
     *                     @OA\Property(property="title", type="string", description="Название услуги", example="Консультация"),
     *                     @OA\Property(property="description", type="string", description="Описание услуги", example="Профессиональная консультация по услугам"),
     *                     @OA\Property(property="price", type="number", format="float", description="Стоимость услуги", example=99.99),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         description="Категория услуги",
     *                         @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                         @OA\Property(property="title", type="string", description="Название категории", example="Консультационные услуги")
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         description="Список URL изображений услуги",
     *                         @OA\Items(
     *                             type="string",
     *                             format="url",
     *                             description="URL изображения",
     *                             example="https://example.com/images/service.jpg"
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Неавторизован", description="Сообщение об ошибке"),
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
     * @OA\Post(
     *     path="/api/admin/services",
     *     tags={"Admin/Services"},
     *     summary="Создать новую услугу",
     *     operationId="createServiceAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "price", "category_id"},
     *                 @OA\Property(property="title", type="string", description="Название услуги", example="Консультация", maxLength=255),
     *                 @OA\Property(property="description", type="string", description="Описание услуги", example="Профессиональная консультация по услугам"),
     *                 @OA\Property(property="price", type="number", format="float", description="Стоимость услуги", example=99.99, minimum=0),
     *                 @OA\Property(property="category_id", type="string", description="Идентификатор категории услуги", example="1"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для услуги",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Изображение в формате jpg, jpeg, png или webp (макс. 2MB)"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Услуга создана",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"created"}, example="created", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Услуга создана", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Созданная услуга",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги"),
     *                 @OA\Property(property="title", type="string", description="Название услуги", example="Консультация"),
     *                 @OA\Property(property="description", type="string", description="Описание услуги", example="Профессиональная консультация по услугам"),
     *                 @OA\Property(property="price", type="number", format="float", description="Стоимость услуги", example=99.99),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     description="Категория услуги",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                     @OA\Property(property="title", type="string", description="Название категории", example="Консультационные услуги")
     *                 ),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Список URL изображений услуги",
     *                     @OA\Items(
     *                         type="string",
     *                         format="url",
     *                         description="URL изображения",
     *                         example="https://example.com/images/service.jpg"
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Неавторизован", description="Сообщение об ошибке"),
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
    public function store(ServiceRequest $request)
    {
        $service = $this->serviceService->addService($request->only(['title', 'description', 'price', 'category_id']));

        if ($request->hasFile('images')) {
            $this->imageService->addImages($request->file('images'), $service);
        }

        return response()->json([
            'status' => 'created',
            'message' => 'Услуга создана',
            'data' => new ServiceResource($service)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/services/{id}",
     *     tags={"Admin/Services"},
     *     summary="Обновить услугу по идентификатору",
     *     operationId="updateServiceAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор услуги",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "price", "_method"},
     *                 @OA\Property(property="_method", type="string", enum={"PATCH"}, description="Метод HTTP для эмуляции PATCH", example="PATCH"),
     *                 @OA\Property(property="title", type="string", description="Название услуги", example="Обновленная консультация", maxLength=255),
     *                 @OA\Property(property="description", type="string", description="Описание услуги", example="Обновленное описание консультации"),
     *                 @OA\Property(property="price", type="number", format="float", description="Стоимость услуги", example=149.99, minimum=0),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для услуги",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Изображение в формате jpg, jpeg, png или webp (макс. 2MB)"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Услуга обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Услуга обновлена", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после обновления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Неавторизован", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Услуга не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Услуга не найдена", description="Сообщение об ошибке"),
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
    public function update(ServiceRequest $request, string $id)
    {
        try {
            $service = $this->serviceService->updateService($request->only('title', 'description', 'price'), $id);

            if ($request->hasFile('images')) {
                $this->imageService->updateImages($request->file('images'), $service);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Услуга не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Услуга обновлена',
            'data' => null
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/services/{id}",
     *     tags={"Admin/Services"},
     *     summary="Удалить услугу по идентификатору",
     *     operationId="deleteServiceAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор услуги",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Услуга удалена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Услуга удалена", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после удаления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Неавторизован", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Услуга не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Услуга не найдена", description="Сообщение об ошибке"),
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
        $this->serviceService->deleteServiceById($id);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Услуга удалена',
            'data' => null
        ]);
    }
}
