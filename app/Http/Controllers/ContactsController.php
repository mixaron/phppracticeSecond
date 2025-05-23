<?php

namespace App\Http\Controllers;

use App\Domains\Contacts\Resources\ContactsResource;
use App\Services\ContactsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactsController extends Controller
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
     *     path="/api/contacts",
     *     tags={"Contacts"},
     *     summary="Получить контактные данные",
     *     operationId="getPublicContacts",
     *     @OA\Response(
     *         response=200,
     *         description="Контактные данные",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Контакты", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив контактных данных",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор контактов"),
     *                     @OA\Property(property="address", type="string", description="Адрес", example="г. Москва, ул. Примерная, д. 1"),
     *                     @OA\Property(property="phone", type="string", description="Номер телефона", example="+79991234567"),
     *                     @OA\Property(property="email", type="string", description="Электронная почта", example="contact@example.com"),
     *                     @OA\Property(property="work_time", type="string", description="Рабочее время", example="Пн-Пт: 9:00-18:00"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *                 )
     *             )
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
}
