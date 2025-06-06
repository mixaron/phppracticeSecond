<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Models\User;
use App\Domains\User\Models\UserRequest;
use Illuminate\Support\Collection;

class UserRequestRepository
{
    public function __construct()
    {
    }

    public function findAllRequestsByUser(User $user): Collection
    {
        return $user->userRequests()->getResults();
    }

    public function addRequest(array $data): void
    {
        UserRequest::create($data);
    }

    public function save(UserRequest $userRequest): void
    {
        $userRequest->save();
    }

    public function findAllRequests(): Collection
    {
        return UserRequest::all();
    }

    public function findRequestById(int $id): UserRequest
    {
        return UserRequest::findOrFail($id);
    }

}
