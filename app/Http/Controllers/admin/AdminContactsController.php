<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactsRequest;
use App\Http\Resources\ContactsResource;
use App\Services\ContactsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AdminContactsController extends Controller
{
    private ContactsService $contactsService;

    /**
     * @param ContactsService $contactsService
     */
    public function __construct(ContactsService $contactsService)
    {
        $this->contactsService = $contactsService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/contacts",
     *     tags={"Admin/Contacts"},
     *     summary="Получить контактные данные",
     *     operationId="getContacts",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Контактные данные",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Контактные данные",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор контактов"),
     *                 @OA\Property(property="address", type="string", description="Адрес", example="г. Москва, ул. Примерная, д. 1"),
     *                 @OA\Property(property="phone", type="string", description="Номер телефона", example="+79991234567"),
     *                 @OA\Property(property="email", type="string", description="Электронная почта", example="contact@example.com"),
     *                 @OA\Property(property="work_time", type="string", description="Рабочее время", example="Пн-Пт: 9:00-18:00"),
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
     *         description="Контакты не найдены",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты не найдены", description="Сообщение об ошибке"),
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
        try {
            $footer = $this->contactsService->getEntityWithCache(1);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Контакты не найдены',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Контакты',
            'data' => new ContactsResource($footer)
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/contacts/{id}",
     *     tags={"Admin/Contacts"},
     *     summary="Обновить контактные данные",
     *     operationId="updateContacts",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор контактных данных",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"address", "phone", "email", "work_time"},
     *             @OA\Property(property="address", type="string", description="Адрес", example="г. Москва, ул. Примерная, д. 1", maxLength=255),
     *             @OA\Property(property="phone", type="string", description="Номер телефона", example="+79991234567", maxLength=12, minLength=12),
     *             @OA\Property(property="email", type="string", description="Электронная почта", example="contact@example.com", maxLength=255),
     *             @OA\Property(property="work_time", type="string", description="Рабочее время", example="Пн-Пт: 9:00-18:00", maxLength=255)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Контакты обновлены",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты обновлены", description="Сообщение о результате запроса"),
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
     *         description="Контакты не найдены",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты не найдены", description="Сообщение об ошибке"),
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
    public function update(ContactsRequest $contactsRequest, string $id)
    {
        $status = 'success';
        $message = 'Контакты обновлены';
        $code = 200;
        try {
            $this->contactsService->updateContacts($contactsRequest->only(
                ['address', 'phone', 'email', 'work_time']), $id);
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Контакты не найдены';
            $code = 404;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/contacts",
     *     tags={"Admin/Contacts"},
     *     summary="Создать новые контактные данные",
     *     operationId="createContacts",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"address", "phone", "email", "work_time"},
     *             @OA\Property(property="address", type="string", description="Адрес", example="г. Москва, ул. Примерная, д. 1", maxLength=255),
     *             @OA\Property(property="phone", type="string", description="Номер телефона", example="+79991234567", maxLength=12, minLength=12),
     *             @OA\Property(property="email", type="string", description="Электронная почта", example="contact@example.com", maxLength=255),
     *             @OA\Property(property="work_time", type="string", description="Рабочее время", example="Пн-Пт: 9:00-18:00", maxLength=255)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Контакты созданы",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"created"}, example="created", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты созданы", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Созданные контактные данные",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор контактов"),
     *                 @OA\Property(property="address", type="string", description="Адрес", example="г. Москва, ул. Примерная, д. 1"),
     *                 @OA\Property(property="phone", type="string", description="Номер телефона", example="+79991234567"),
     *                 @OA\Property(property="email", type="string", description="Электронная почта", example="contact@example.com"),
     *                 @OA\Property(property="work_time", type="string", description="Рабочее время", example="Пн-Пт: 9:00-18:00"),
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
    public function store(ContactsRequest $request)
    {
        $contacts = $this->contactsService->addContacts($request->only(['address', 'phone', 'email', 'work_time']));

        return response()->json([
            'status' => 'created',
            'message' => 'Контакты созданы',
            'data' => new ContactsResource($contacts)
        ]);
    }
}
