<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminServiceController extends Controller
{
    private ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }
    /**
     * @OA\Post(
     *     path="/api/service",
     *     tags={"Service"},
     *     summary="Создасть услугу",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "description", "price"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *              @OA\Property(property="price", type="decimal")
     *         )
     *     ),
     *     @OA\Response(response=201, description="CREATED")
     * )
     */
    public function store(ServiceRequest $request)
    {
        $this->serviceService->addService($request->only(['title', 'description', 'price']));

        return response()->json([
            'status' => 'created',
            'message' => 'Услуга создана',
            'data' => null
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/service",
     *     tags={"Service"},
     *     summary="Обновить услугу",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string")
     *              @OA\Property(property="price", type="decimal")
     *         )
     *     ),
     *     @OA\Response(response=200, description="UPDATED")
     * )
     *
     */
    public function update(ServiceRequest $request, string $id)
    {

        try {
            $this->serviceService->updateService($request->only('title', 'description', 'price'), $id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Услуга не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'updated',
            'message' => 'Услуга обновлена',
            'data' => null
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/service/{id}",
     *     tags={"Service"},
     *     summary="Удалить услугу по id",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=204, description="Deleted")
     * )
     */
    public function destroy(string $id)
    {
        $this->serviceService->deleteServiceById($id);

        return response()->json([
            'status' => 'deleted',
            'message' => 'Услуга удалена',
            'data' => null
        ]);
    }
}
