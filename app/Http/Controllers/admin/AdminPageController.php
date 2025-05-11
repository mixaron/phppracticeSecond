<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Services\ServiceService;
use App\Services\UserService;

class AdminPageController extends Controller
{
    private NewsService $newsService;
    private ServiceService $serviceService;
    private UserService $userService;

    /**
     * @param NewsService $newsService
     * @param ServiceService $serviceService
     * @param UserService $userService
     */
    public function __construct(NewsService $newsService, ServiceService $serviceService, UserService $userService)
    {
        $this->newsService = $newsService;
        $this->serviceService = $serviceService;
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/information",
     *     tags={"Admin"},
     *     summary="Получить статистику по пользователям, новостям и услугам",
     *     operationId="getAdminInformation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Статистика по количеству пользователей, новостей и услуг",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Кол-во пользователей, новостей и услуг", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Данные статистики",
     *                 @OA\Property(property="user_count", type="integer", description="Количество пользователей", example=100),
     *                 @OA\Property(property="news_count", type="integer", description="Количество новостей", example=50),
     *                 @OA\Property(property="service_count", type="integer", description="Количество услуг", example=30)
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

        return response()->json([
            'status' => 'success',
            'message' => 'Кол-во пользователей, новостей и услуг',
            'data' => [
                'user_count' => $userCount,
                'news_count' => $newsCount,
                'service_count' => $serviceCount,
            ]
        ]);
    }
}
