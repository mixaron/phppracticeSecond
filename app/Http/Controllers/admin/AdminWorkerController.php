<?php

namespace App\Http\Controllers\admin;

use App\Domains\Worker\Resources\WorkerResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerRequest;
use App\Services\ImageService;
use App\Services\WorkerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminWorkerController extends Controller
{
    private WorkerService $workerService;
    private ImageService $imageService;

    public function __construct(WorkerService $workerService, ImageService $imageService)
    {
        $this->workerService = $workerService;
        $this->imageService = $imageService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/workers",
     *     tags={"Admin/Workers"},
     *     summary="Получить список всех работников",
     *     operationId="getAllWorkersAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список работников",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список работников", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив работников",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор работника"),
     *                     @OA\Property(property="firstname", type="string", description="Имя работника", example="Иван"),
     *                     @OA\Property(property="lastname", type="string", description="Фамилия работника", example="Иванов"),
     *                     @OA\Property(property="age", type="integer", description="Возраст работника", example=30),
     *                     @OA\Property(property="description", type="string", description="Описание работника", example="Опытный специалист"),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         description="Список URL изображений работника",
     *                         @OA\Items(
     *                             type="string",
     *                             format="url",
     *                             description="URL изображения",
     *                             example="https://example.com/storage/images/worker.jpg"
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
    public function index()
    {
        $workers = $this->workerService->getListWithCache(null);

        return response()->json([
            'status' => 'success',
            'message' => 'Список работников',
            'data' => WorkerResource::collection($workers)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/workers",
     *     tags={"Admin/Workers"},
     *     summary="Создать нового работника",
     *     operationId="createWorkerAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"firstname", "lastname", "age", "description"},
     *                 @OA\Property(property="firstname", type="string", description="Имя работника", example="Иван", maxLength=255),
     *                 @OA\Property(property="lastname", type="string", description="Фамилия работника", example="Иванов", maxLength=255),
     *                 @OA\Property(property="age", type="integer", description="Возраст работника", example=30, minimum=18, maximum=99),
     *                 @OA\Property(property="description", type="string", description="Описание работника", example="Опытный специалист", maxLength=255),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для работника",
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
     *         description="Работник создан",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"created"}, example="created", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Работник создан", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Созданный работник",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор работника"),
     *                 @OA\Property(property="firstname", type="string", description="Имя работника", example="Иван"),
     *                 @OA\Property(property="lastname", type="string", description="Фамилия работника", example="Иванов"),
     *                 @OA\Property(property="age", type="integer", description="Возраст работника", example=30),
     *                 @OA\Property(property="description", type="string", description="Описание работника", example="Опытный специалист"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Список URL изображений работника",
     *                     @OA\Items(
     *                         type="string",
     *                         format="url",
     *                         description="URL изображения",
     *                         example="https://example.com/storage/images/worker.jpg"
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
    public function store(WorkerRequest $request)
    {
        $worker = $this->workerService->addWorker($request->only([
            'firstname', 'lastname', 'age', 'description'
        ]));

        if ($request->hasFile('images')) {
            $this->imageService->addImages($request->file('images'), $worker);
        }

        return response()->json([
            'status' => 'created',
            'message' => 'Работник создан',
            'data' => new WorkerResource($worker)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/workers/{id}",
     *     tags={"Admin/Workers"},
     *     summary="Обновить работника по идентификатору",
     *     operationId="updateWorkerAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор работника",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"firstname", "lastname", "age", "description", "_method"},
     *                 @OA\Property(property="_method", type="string", enum={"PATCH"}, description="Метод HTTP для эмуляции PATCH", example="PATCH"),
     *                 @OA\Property(property="firstname", type="string", description="Имя работника", example="Иван", maxLength=255),
     *                 @OA\Property(property="lastname", type="string", description="Фамилия работника", example="Иванов", maxLength=255),
     *                 @OA\Property(property="age", type="integer", description="Возраст работника", example=30, minimum=18, maximum=99),
     *                 @OA\Property(property="description", type="string", description="Описание работника", example="Опытный специалист", maxLength=255),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для работника",
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
     *         description="Работник обновлён",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Работник обновлён", description="Сообщение о результате запроса"),
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
     *         description="Работник не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Работник не найден", description="Сообщение об ошибке"),
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
    public function update(WorkerRequest $request, string $id)
    {
        try {
            $worker = $this->workerService->updateWorker($request->only([
                'firstname', 'lastname', 'age', 'description'
            ]), $id);

            if ($request->hasFile('images')) {
                $this->imageService->updateImages($request->file('images'), $worker);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Работник не найден',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Работник обновлён',
            'data' => null
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/workers/{id}",
     *     tags={"Admin/Workers"},
     *     summary="Удалить работника по идентификатору",
     *     operationId="deleteWorkerAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор работника",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Работник удалён",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Работник удалён", description="Сообщение о результате запроса"),
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
     *         description="Работник не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Работник не найден", description="Сообщение об ошибке"),
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
        $this->workerService->deleteWorkerById($id);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Работник удалён',
            'data' => null
        ]);
    }
}
