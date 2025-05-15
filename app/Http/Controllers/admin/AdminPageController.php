<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EditUserResource;
use App\Http\Resources\UserRequestResource;
use App\Models\User;
use App\Services\NewsService;
use App\Services\ReviewService;
use App\Services\ServiceService;
use App\Services\UserService;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    private NewsService $newsService;
    private ServiceService $serviceService;
    private UserService $userService;
    private ReviewService $reviewService;

    /**
     * @param NewsService $newsService
     * @param ServiceService $serviceService
     * @param UserService $userService
     */
    public function __construct(NewsService $newsService, ServiceService $serviceService,
                                UserService $userService, ReviewService $reviewService)
    {
        $this->newsService = $newsService;
        $this->serviceService = $serviceService;
        $this->userService = $userService;
        $this->reviewService = $reviewService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/information",
     *     tags={"Admin/Page"},
     *     summary="Получить статистику по пользователям, новостям и услугам",
     *     operationId="getAdminInformation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Статистика по количеству пользователей, новостей и услуг",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Кол-во пользователей, новостей, услуг и пользователей", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные статистики",
     *                 @OA\Property(property="user_count", type="integer", description="Количество пользователей", example=100),
     *                 @OA\Property(property="news_count", type="integer", description="Количество новостей", example=50),
     *                 @OA\Property(property="service_count", type="integer", description="Количество услуг", example=30),
     *                 @OA\Property(property="ReviewsCount", type="integer", description="Количество отзывов", example=30)
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
    public function getInformation()
    {
        $userCount = $this->userService->getUsersCount();
        $newsCount = $this->newsService->getNewsCount();
        $serviceCount = $this->serviceService->getServiceCount();
        $reviewCount = $this->reviewService->getReviewCount();
        return response()->json([
            'status' => 'success',
            'message' => 'Кол-во пользователей, новостей и услуг',
            'data' => [
                'user_count' => $userCount,
                'news_count' => $newsCount,
                'service_count' => $serviceCount,
                'reviews_count' => $reviewCount
            ]
        ]);
    }

    public function getAllUsers()
    {
        $users = $this->userService->getAllUsers();
        return response()->json([
            'status' => 'success',
            'message' => 'Список пользователей',
            'data' => EditUserResource::collection($users)
        ]);
    }

    public function deleteUser(string $id)
    {
        $this->userService->deleteUserById($id);
        return response()->json([
            'status' => 'deleted',
            'message' => 'Пользователь удален',
            'data' => null
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/users/{id}/role",
     *     tags={"Admin/Page"},
     *     summary="Изменить роль пользователя",
     *     operationId="changeUserRole",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор пользователя",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"role"},
     *             @OA\Property(
     *                 property="role",
     *                 type="string",
     *                 enum={"user", "admin"},
     *                 description="Новая роль пользователя",
     *                 example="admin"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Роль пользователя обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Роль пользователя обновлена", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после обновления роли)")
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
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Пользователь не найден", description="Сообщение об ошибке"),
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
    public function changeUserRole(Request $request, string $id)
    {
        $request->validate([
            'role' => 'required|string|in:user,admin'
        ]);

        $this->userService->changeRole($id, $request->input('role'));

        return response()->json([
            'status' => 'success',
            'message' => 'Роль пользователя обновлена',
            'data' => null
        ]);
    }

}
