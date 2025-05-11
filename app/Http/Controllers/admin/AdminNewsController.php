<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Services\NewsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminNewsController extends Controller
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

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
     * @OA\Post(
     *     path="/api/news",
     *     tags={"News"},
     *     summary="Создасть новость",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="CREATED")
     * )
     */
    public function store(NewsRequest $request)
    {
        $this->newsService->addNews($request->only(['title', 'description', 'category_id']));

        return response()->json([
            'status' => 'created',
            'message' => 'Новость создана',
            'data' => null
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/news",
     *     tags={"News"},
     *     summary="Обновить новость",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="UPDATED")
     * )
     *
     */
    public function update(NewsRequest $request, string $id)
    {
        try {
            $this->newsService->updateNews($request->only('title', 'description', 'category_id'), $id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Новость не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Новость обновлена',
            'data' => null
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/news/{id}",
     *     tags={"News"},
     *     summary="Удалить новость по id",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(string $id)
    {
        $this->newsService->deleteNewsById($id);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Новость успешно удалена',
            'data' => null
        ]);
    }
}
