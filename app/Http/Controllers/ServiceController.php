<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceController extends Controller
{
    private ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * @OA\Get(
     *     path="/api/service",
     *     tags={"Service"},
     *     summary="Список всех услуг",
     *     @OA\Response(response=200, description="Лист услуг")
     * )
     */
    public function index()
    {
        $allServices = $this->serviceService->getAllServices();

        return response()->json([
            'status' => 'success',
            'message' => 'Список услуг',
            'data' => ServiceResource::collection($allServices)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/service/{id}",
     *     tags={"Service"},
     *     summary="Получить услугу по id",
     *     @OA\Response(response=200, description="Услуга")
     * )
     */
    public function show(string $id)
    {
        try {
            $news = $this->serviceService->getServiceById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Услуга не найдена',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Услуга по id',
            'data' => new ServiceResource($news)
        ]);
    }
}
