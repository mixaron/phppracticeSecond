<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserRequestResource;
use App\Services\UserRequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserRequestController extends Controller
{
    private UserRequestService $userRequestService;

    public function __construct(UserRequestService $userRequestService)
    {
        $this->userRequestService = $userRequestService;
    }

    /**
     * @OA\Get(
     *     path="/api/request",
     *     tags={"Requests"},
     *     summary="Получить список всех заявок пользователя",
     *     operationId="getAllUserRequests",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список заявок пользователя",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список заявок пользователя", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив заявок пользователя",
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
    public function index()
    {
        $user = auth()->user();
        $userRequests = $this->userRequestService->findAllRequestsByUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Список заявок пользователя',
            'data' => UserRequestResource::collection($userRequests)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/request",
     *     tags={"Requests"},
     *     summary="Создать новую заявку",
     *     operationId="createUserRequest",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string", description="Название заявки", example="Новая заявка"),
     *             @OA\Property(property="description", type="string", description="Описание заявки", example="Описание проблемы"),
     *             @OA\Property(property="service_id", type="string", description="Идентификатор услуги", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заявка создана",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"created", "error"},
     *                 example="created",
     *                 description="Статус запроса"
     *             ),
     *             @OA\Items(
     *                 type="string",
     *                 example="Заявка создана",
     *                 description="Сообщение о результате запроса"
     *             ),
     *             @OA\Items(
     *                 type="object",
     *                 example=null,
     *                 description="Данные (отсутствуют после создания)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"error"},
     *                 example="error",
     *                 description="Статус запроса"
     *             ),
     *             @OA\Items(
     *                 type="string",
     *                 example="Неавторизован",
     *                 description="Сообщение об ошибке"
     *             ),
     *             @OA\Items(
     *                 type="object",
     *                 example=null,
     *                 description="Данные (отсутствуют при ошибке)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Услуга не найдена",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"error"},
     *                 example="error",
     *                 description="Статус запроса"
     *             ),
     *             @OA\Items(
     *                 type="string",
     *                 example="Услуги не существует",
     *                 description="Сообщение об ошибке"
     *             ),
     *             @OA\Items(
     *                 type="object",
     *                 example=null,
     *                 description="Данные (отсутствуют при ошибке)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"error"},
     *                 example="error",
     *                 description="Статус запроса"
     *             ),
     *             @OA\Items(
     *                 type="string",
     *                 example="Ошибка валидации",
     *                 description="Сообщение об ошибке"
     *             ),
     *             @OA\Items(
     *                 type="object",
     *                 description="Детали ошибки валидации"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"error"},
     *                 example="error",
     *                 description="Статус запроса"
     *             ),
     *             @OA\Items(
     *                 type="string",
     *                 example="Произошла непредвиденная ошибка",
     *                 description="Сообщение об ошибке"
     *             ),
     *             @OA\Items(
     *                 type="object",
     *                 example=null,
     *                 description="Данные (отсутствуют при ошибке)"
     *             )
     *         )
     *     )
     * )
     */
    public function store(UserRequest $request)
    {
        $status = 'created';
        $message = 'Заявка создана';
        $code = 200;
        try {
            $this->userRequestService->addRequest([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'service_id' => $request->input('service_id'),
                'status' => 'new',
                'user_id' => auth()->id()
            ]);
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Услуги не существует';
            $code = 404;
        }

        return response()->json([
            $status,
            $message,
            'data' => null
        ], $code);
    }

    /**
     * @OA\Get(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Получить заявку пользователя по идентификатору",
     *     operationId="getUserRequestById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор заявки",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные заявки",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка получена", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные заявки",
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
        $user = auth()->user();

        try {
            $userRequest = $this->userRequestService->showRequestById($id, $user);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Заявка получена',
            'data' => new UserRequestResource($userRequest)
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Обновить заявку пользователя по идентификатору",
     *     operationId="updateUserRequest",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор заявки",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", description="Название заявки", example="Обновленная заявка"),
     *             @OA\Property(property="description", type="string", description="Описание заявки", example="Обновленное описание"),
     *             @OA\Property(property="service_id", type="string", description="Идентификатор услуги", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заявка обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка обновлена", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные заявки",
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
     *         response=400,
     *         description="Заявка уже в процессе решения",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка уже в процессе решения", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
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
    public function update(UserRequest $request, string $id)
    {
        $user = auth()->user();

        try {
            $userRequest = $this->userRequestService
                ->updateRequest($request->only(['title', 'description', 'service_id']), $id, $user);
        } catch (BadRequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка уже в процессе решения',
                'data' => null
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Заявка обновлена',
            'data' => new UserRequestResource($userRequest)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/request/{id}",
     *     tags={"Requests"},
     *     summary="Удалить заявку пользователя по идентификатору",
     *     operationId="deleteUserRequest",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор заявки",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заявка удалена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка удалена", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после удаления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Заявка уже в процессе решения",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Заявка уже в процессе решения", description="Сообщение об ошибке"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют при ошибке)")
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
        $user = auth()->user();

        try {
            $this->userRequestService->deleteById($id, $user);
        } catch (BadRequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка уже в процессе решения',
                'data' => null
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Заявка не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'deleted',
            'message' => 'Заявка удалена',
            'data' => null
        ]);
    }
}
