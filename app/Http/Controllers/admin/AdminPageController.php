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
//    private ReviewService $reviewService;

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
