<?php

namespace App\Services;

use App\Domains\Service\Models\Service;
use App\Domains\User\Models\User;
use App\Domains\User\Models\UserRequest;
use App\Domains\User\Repositories\UserRequestRepository;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserRequestService
{
    private UserRequestRepository $userRequestRepository;

    public function __construct(UserRequestRepository $userRequestRepository)
    {
        $this->userRequestRepository = $userRequestRepository;
    }

    public function findAllRequestsByUser($user): Collection
    {
        return $this->userRequestRepository->findAllRequestsByUser($user);
    }

    public function addRequest(array $data): void
    {
        Service::findOrFail($data['service_id']);
        $this->userRequestRepository->addRequest($data);
    }

    public function showRequestById(string $id, User $user): UserRequest
    {
        return $user->userRequests()->findOrFail($id);
    }

    public function updateRequest(array $data, string $id, User $user): UserRequest
    {
        $userRequest = $user->userRequests()->findOrFail($id);


        if (isset($data['title'])) $userRequest->title = $data['title'];
        if (isset($data['description'])) $userRequest->description = $data['description'];

        $this->userRequestRepository->save($userRequest);

        return $userRequest;
    }

    public function deleteById(string $id, User $user): void
    {
        $userRequest = $user->userRequests()->findOrFail($id);

        if ($userRequest->status !== 'new') throw new BadRequestException();

        $userRequest->delete();
    }
}
