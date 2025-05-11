<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Repositories\UserRequestRepository;
use Illuminate\Support\Collection;

class RequestService
{

    private UserRequestRepository $userRequestRepository;

    public function __construct(UserRequestRepository $userRequestRepository)
    {
        $this->userRequestRepository = $userRequestRepository;
    }

    public function getAllRequests(): Collection
    {
        return $this->userRequestRepository->findAllRequests();
    }

    public function changeStatus(int $id, mixed $status): \App\Models\UserRequest
    {
        $request = $this->userRequestRepository->findRequestById($id);
        $request->status = $status;
        $this->userRequestRepository->save($request);

        return $request;
    }
}
