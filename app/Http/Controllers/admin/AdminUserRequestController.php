<?php

namespace App\Http\Controllers\admin;

use App\Domains\User\Resources\UserRequestResource;
use App\Http\Controllers\Controller;
use App\Services\RequestService;
use Illuminate\Http\Request;

class AdminUserRequestController extends Controller
{
    private RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/requests",
     *     tags={"Admin/Requests"},
     *     summary="Получить список всех заявок пользователей",
     *     operationId="getAllRequestsAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список всех заявок пользователей",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Все заявки пользователей", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив заявок пользователей",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор заявки"),
     *                     @OA\Property(property="title", type="string", description="Название заявки"),
     *                     @OA\Property(property="description", type="string", description="Описание заявки"),
     *                     @OA\Property(property="status", type="string", description="Статус заявки"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         description="Информация о пользователе",
     *                         @OA\Property(property="id", type="string", description="Уникальный идентификатор пользователя"),
     *                         @OA\Property(property="name", type="string", description="Имя пользователя")
     *                     ),
     *                     @OA\Property(
     *                         property="service",
     *                         type="object",
     *                         description="Информация об услуге",
     *                         @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги"),
     *                         @OA\Property(property="title", type="string", description="Название услуги")
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
    public function showRequests()
    {
        $requests = $this->requestService->getAllRequests();

        return response()->json([
            'status' => "success",
            'message' => 'Все заявки пользователей',
            'data' => UserRequestResource::collection($requests)
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/requests/{id}/status",
     *     tags={"Admin/Requests"},
     *     summary="Обновить статус заявки по идентификатору",
     *     operationId="changeRequestStatusAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор заявки",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"new", "in_progress", "completed", "cancelled", "rejected"},
     *                 description="Новый статус заявки",
     *                 example="in_progress"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Статус заявки обновлён",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Статус заявки обновлён", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные обновлённой заявки",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор заявки"),
     *                 @OA\Property(property="title", type="string", description="Название заявки"),
     *                 @OA\Property(property="description", type="string", description="Описание заявки"),
     *                 @OA\Property(property="status", type="string", description="Статус заявки"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     description="Информация о пользователе",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор пользователя"),
     *                     @OA\Property(property="name", type="string", description="Имя пользователя")
     *                 ),
     *                 @OA\Property(
     *                     property="service",
     *                     type="object",
     *                     description="Информация об услуге",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор услуги"),
     *                     @OA\Property(property="title", type="string", description="Название услуги")
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
    public function changeStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,in_progress,completed,cancelled,rejected',
        ]);

        $updatedRequest = $this->requestService->changeStatus($id, $validated['status']);

        return response()->json([
            'status' => 'success',
            'message' => 'Статус заявки обновлён',
            'data' => new UserRequestResource($updatedRequest)
        ]);
    }
}
