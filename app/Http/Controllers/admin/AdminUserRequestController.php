<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Http\Resources\UserRequestResource;
use App\Services\RequestService;
use Illuminate\Http\Request;

class AdminUserRequestController extends Controller
{
    private RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }
    public function showRequests()
    {
        $requests = $this->requestService->getAllRequests();

        return response()->json([
            'status' => "success",
            'message' => 'Все заявки пользователей',
            'data' => UserRequestResource::collection($requests)
        ]);
    }

    public function changeStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,in_progress,completed,cancelled,rejected',
        ]);

        $updatedRequest = $this->requestService->changeStatus($id, $validated['status']);

        return response()->json([
            'status' => 'success',
            'message' => 'Статус заявки обновлён',
            'data' => new UserRequestResource($updatedRequest)
        ]);
    }

}
