<?php

namespace App\Http\Controllers\admin;

use App\Domains\News\Resources\NewsResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Services\ImageService;
use App\Services\NewsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AdminNewsController extends Controller
{
    private NewsService $newsService;
    private ImageService $imageService;

    public function __construct(NewsService $newsService, \App\Services\ImageService $imageService)
    {
        $this->newsService = $newsService;
        $this->imageService = $imageService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/news",
     *     tags={"Admin/News"},
     *     summary="Получить список всех новостей",
     *     operationId="getAllNewsAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Идентификатор категории для фильтрации новостей",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список новостей",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"success"}, example="success", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Список новостей", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Массив новостей",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор новости"),
     *                     @OA\Property(property="title", type="string", description="Заголовок новости", example="Новая новость"),
     *                     @OA\Property(property="description", type="string", description="Описание новости", example="Описание новой новости"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         description="Категория новости",
     *                         @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                         @OA\Property(property="title", type="string", description="Название категории", example="Объявления")
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         description="Список URL изображений новости",
     *                         @OA\Items(
     *                             type="string",
     *                             format="url",
     *                             description="URL изображения",
     *                             example="https://example.com/images/news.jpg"
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Дата и время создания в формате ISO 8601"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата и время последнего обновления в формате ISO 8601")
     *                 )
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
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');
        $categoryId = is_numeric($categoryId) ? (int)$categoryId : null;
        $newsWithCache = $this->newsService->getListWithCache($categoryId);

        return response()->json([
            'status' => 'success',
            'message' => 'Список новостей',
            'data' => NewsResource::collection($newsWithCache)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/news",
     *     tags={"Admin/News"},
     *     summary="Создать новую новость",
     *     operationId="createNewsAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "category_id"},
     *                 @OA\Property(property="title", type="string", description="Заголовок новости", example="Новая новость", maxLength=255),
     *                 @OA\Property(property="description", type="string", description="Описание новости", example="Описание новой новости"),
     *                 @OA\Property(property="category_id", type="string", description="Идентификатор категории новости", example="1"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для новости",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Изображение в формате jpg, jpeg, png или webp (макс. 2MB)"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Новость создана",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"created"}, example="created", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость создана", description="Сообщение о результате запроса"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Созданная новость",
     *                 @OA\Property(property="id", type="string", description="Уникальный идентификатор новости"),
     *                 @OA\Property(property="title", type="string", description="Заголовок новости", example="Новая новость"),
     *                 @OA\Property(property="description", type="string", description="Описание новости", example="Описание новой новости"),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     description="Категория новости",
     *                     @OA\Property(property="id", type="string", description="Уникальный идентификатор категории"),
     *                     @OA\Property(property="title", type="string", description="Название категории", example="Объявления")
     *                 ),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Список URL изображений новости",
     *                     @OA\Items(
     *                         type="string",
     *                         format="url",
     *                         description="URL изображения",
     *                         example="https://example.com/images/news.jpg"
     *                     )
     *                 ),
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
    public function store(NewsRequest $request)
    {
        $news = $this->newsService->addNews($request->only(['title', 'description', 'category_id']));

        if ($request->hasFile('images')) {
            $this->imageService->addImages($request->file('images'), $news);
        }
        return response()->json([
            'status' => 'created',
            'message' => 'Новость создана',
            'data' => new NewsResource($news)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/news/{id}",
     *     tags={"Admin/News"},
     *     summary="Обновить новость по идентификатору",
     *     operationId="updateNewsAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор новости",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "category_id", "_method"},
     *                 @OA\Property(property="_method", type="string", enum={"PATCH"}, description="Метод HTTP для эмуляции PATCH", example="PATCH"),
     *                 @OA\Property(property="title", type="string", description="Заголовок новости", example="Обновленная новость", maxLength=255),
     *                 @OA\Property(property="description", type="string", description="Описание новости", example="Обновленное описание новости"),
     *                 @OA\Property(property="category_id", type="string", description="Идентификатор категории новости", example="1"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Массив изображений для новости",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Изображение в формате jpg, jpeg, png или webp (макс. 2MB)"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Новость обновлена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"updated"}, example="updated", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость обновлена", description="Сообщение о результате запроса"),
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
     *         description="Новость или категория не найдены",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость не найдена", description="Сообщение об ошибке"),
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
    public function update(NewsRequest $request, string $id)
    {
        try {
            $service = $this->newsService->updateNews($request->only('title', 'description', 'category_id'), $id);

            if ($request->hasFile('images')) {
                $this->imageService->updateImages($request->file('images'), $service);
            }
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
     *     path="/api/admin/news/{id}",
     *     tags={"Admin/News"},
     *     summary="Удалить новость по идентификатору",
     *     operationId="deleteNewsAdmin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Идентификатор новости",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Новость удалена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"deleted"}, example="deleted", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость успешно удалена", description="Сообщение о результате запроса"),
     *             @OA\Property(property="data", type="null", example=null, description="Данные (отсутствуют после удаления)")
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
     *         description="Новость не найдена",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", enum={"error"}, example="error", description="Статус запроса"),
     *             @OA\Property(property="message", type="string", example="Новость не найдена", description="Сообщение об ошибке"),
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
