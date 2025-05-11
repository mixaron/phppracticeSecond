<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsCategoryResource;
use App\Services\NewsCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsCategoryController extends Controller
{
    private NewsCategoryService $newsCategoryService;

    public function __construct(NewsCategoryService $newsCategoryService)
    {
        $this->newsCategoryService = $newsCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/news",KK
     *     tags={"News"},
     *     summary="Список всех новостей",
     *     @OA\Response(response=200, description="Лист новостей")
     * )
     */
    public function index()
    {
        $newsList = $this->newsCategoryService->getAllNewsCategory();

        return response()->json([
            'status' => 'success',
            'message' => 'Список категорий новостей',
            'data' => NewsCategoryResource::collection($newsList)
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
            $news = $this->newsCategoryService->getNewsCategoryById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категория новости не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Категория новости по id',
            'data' => new NewsCategoryResource($news)
        ]);
    }
}
