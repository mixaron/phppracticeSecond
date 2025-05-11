<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Resources\EditUserResource;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Получить информацию о пользователе",
     *     operationId="getUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Информация о пользователе",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Пользователь", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные пользователя",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор пользователя"),
     *                 @OA\Property(property="name", type="string", description="Имя пользователя"),
     *                 @OA\Property(property="email", type="string", format="email", description="Электронная почта пользователя"),
     *                 @OA\Property(property="phone", type="string", description="Телефон пользователя"),
     *                 @OA\Property(property="role", type="string", description="Роль пользователя"),
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
        $user = $this->userService->getUser();
        return response()->json([
            'status' => 'success',
            'message' => 'Пользователь',
            'data' => new EditUserResource($user)
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Обновить информацию о пользователе",
     *     operationId="updateUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", description="Имя пользователя", example="Иван Иванов"),
     *             @OA\Property(property="email", type="string", format="email", description="Электронная почта пользователя", example="ivan@example.com"),
     *             @OA\Property(property="phone", type="string", description="Телефон пользователя", example="+79991234567"),
     *             @OA\Property(property="role", type="string", description="Роль пользователя", example="user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Обновленный пользователь",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Пользователь обновлен", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные пользователя",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор пользователя"),
     *                 @OA\Property(property="name", type="string", description="Имя пользователя"),
     *                 @OA\Property(property="email", type="string", format="email", description="Электронная почта пользователя"),
     *                 @OA\Property(property="phone", type="string", description="Телефон пользователя"),
     *                 @OA\Property(property="role", type="string", description="Роль пользователя"),
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
    public function update(EditUserRequest $request)
    {
        $user = $this->userService->editUser($request->only(['name', 'email', 'phone']));

        return response()->json([
            'status' => 'updated',
            'message' => 'Пользователь обновлен',
            'data' => new EditUserResource($user)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Удалить пользователя",
     *     operationId="deleteUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Пользователь удален",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Пользователь удален", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после удаления)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Теперь это Неавторизован", description="Сообщение об ошибке"),
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
    public function destroy()
    {
        $user = auth()->user();
        $this->userService->deleteUser($user);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Пользователь удален',
            'data' => null
        ]);
    }
}
