<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsCategoryRequest;
use App\Http\Resources\NewsCategoryResource;
use App\Services\NewsCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminNewsCategoryController extends Controller
{
    private NewsCategoryService $newsCategoryService;

    public function __construct(NewsCategoryService $newsCategoryService)
    {
        $this->newsCategoryService = $newsCategoryService;
    }

    public function index()
    {
        $newsList = $this->newsCategoryService->getAllNewsCategory();

        return response()->json([
            'status' => 'success',
            'message' => 'Список категорий новостей',
            'data' => NewsCategoryResource::collection($newsList)
        ]);
    }
    public function store(NewsCategoryRequest $request)
    {
        $this->newsCategoryService->addNewsCategory($request->only(['title', 'description']));

        return response()->json([
            'status' => 'created',
            'message' => 'Категория для новостей создана',
            'data' => null
        ]);
    }

    public function update(NewsCategoryRequest $request, string $id)
    {
        try {
            $this->newsCategoryService->updateNewsCategory($request->only('title', 'description'), $id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Категория для новостей не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Категория для новостей обновлена',
            'data' => null
        ]);
    }

    public function destroy(string $id)
    {
        $this->newsCategoryService->deleteNewsCategoryById($id);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Категория для новостей удалена',
            'data' => null
        ]);
    }
}
