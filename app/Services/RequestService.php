<?php

namespace App\Services;

use App\Domains\User\Models\User;
use App\Domains\User\Models\UserRequest;
use App\Domains\User\Repositories\UserRequestRepository;
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

    public function changeStatus(int $id, mixed $status): \App\Domains\User\Models\UserRequest
    {
        $request = $this->userRequestRepository->findRequestById($id);
        $request->status = $status;
        $this->userRequestRepository->save($request);

        return $request;
    }

    public function setUserIdByNumber(User $user): void
    {
        UserRequest::where('phone', $user->phone)->update(['user_id' => $user->id]);
    }
}
