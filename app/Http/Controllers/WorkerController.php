<?php

namespace App\Http\Controllers;

use App\Domains\Worker\Resources\WorkerResource;
use App\Services\WorkerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkerController extends Controller
{
    private WorkerService $workerService;

    /**
     * @param WorkerService $workerService
     */
    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    /**
     * @OA\Get(
     *     path="/api/workers",
     *     tags={"Workers"},
     *     summary="Получить список всех специалистов",
     *     operationId="getAllWorkers",
     *     @OA\Response(
     *         response=200,
     *         description="Список всех специалистов",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success", description="Статус ответа"),
     *             @OA\Property(property="message", type="string", example="Список специалистов", description="Сообщение о результате"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Список специалистов",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", description="ID специалиста"),
     *                     @OA\Property(property="firstname", type="string", description="Имя специалиста"),
     *                     @OA\Property(property="lastname", type="string", description="Фамилия специалиста"),
     *                     @OA\Property(property="age", type="integer", description="Возраст специалиста"),
     *                     @OA\Property(property="description", type="string", description="Описание специалиста"),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(type="string", format="uri", example="http://example.com/storage/image.jpg"),
     *                         description="Список изображений"
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function index()
    {
        $workers = $this->workerService->getListWithCache(null);

        return response()->json([
            'status' => 'success',
            'message' => 'Список специалистов',
            'data' => WorkerResource::collection($workers)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/workers/{id}",
     *     tags={"Workers"},
     *     summary="Получить специалиста по ID",
     *     operationId="getWorkerById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID специалиста",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные одного специалиста",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Специалист по id"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="ID специалиста"),
     *                 @OA\Property(property="firstname", type="string", description="Имя"),
     *                 @OA\Property(property="lastname", type="string", description="Фамилия"),
     *                 @OA\Property(property="age", type="integer", description="Возраст"),
     *                 @OA\Property(property="description", type="string", description="Описание"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(type="string", format="uri", example="http://example.com/storage/image.jpg"),
     *                     description="Список изображений"
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Дата создания"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Дата обновления")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Специалист не найден",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Специалист не найден"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Внутренняя ошибка сервера",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Произошла непредвиденная ошибка"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $status = 'success';
        $message = 'Специалист по id';
        $code = 200;
        $worker = null;
        try {
            $worker = $this->workerService->getEntityWithCache($id);
        } catch (ModelNotFoundException $e) {
            $status = 'error';
            $message = 'Специалист не найден';
            $error = 403;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => new WorkerResource($worker)
        ], $code);
    }
}
