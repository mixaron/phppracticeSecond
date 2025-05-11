<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Services\NewsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsController extends Controller
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * @OA\Get(
     *     path="/api/news",
     *     tags={"News"},
     *     summary="Список всех новостей",
     *     @OA\Response(response=200, description="Лист новостей")
     * )
     */
    public function index()
    {
        $newsList = $this->newsService->getAllNews();

        return response()->json([
            'status' => 'success',
            'message' => 'Список новостей',
            'data' => NewsResource::collection($newsList)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/news/{id}",
     *     tags={"News"},
     *     summary="Получить новость по id",
     *     @OA\Response(response=200, description="Новость")
     * )
     */
    public function show(string $id)
    {
        try {
            $news = $this->newsService->getNewsById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Новость не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Новость по id',
            'data' => new NewsResource($news)
        ]);
    }
}
