<?php

namespace App\Services;

use App\Domains\Service\Repositories\ServiceRepository;
use App\Domains\User\Models\User;
use App\Domains\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserService
{
    private UserRepository $userRepository;
    private RequestService $requestService;

    public function __construct(UserRepository $userRepository, RequestService $requestService)
    {
        $this->userRepository = $userRepository;
        $this->requestService = $requestService;
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user =  $this->userRepository->create($data);
        $this->requestService->setUserIdByNumber($user);
        return $user;
    }

    public function getUser(): ?User
    {
        return auth()->user();
    }

    public function editUser(array $data): User
    {
        $user = auth()->user();

        $user->fill($data);
        $this->userRepository->save($user);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->delete($user);
    }

    public function getUsersCount(): int
    {
        return $this->userRepository->count();
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->findAll();
    }

    public function deleteUserById(string $id): void
    {
        $this->userRepository->deleteById($id);
    }

    public function changeRole(string $id, mixed $input): void
    {
        $user = User::findOrFail($id);
        $user->role = $input;
        $user->save();
    }

    public function changePassword(array $array): void
    {
        $user = auth()->user();

        if (!Hash::check($array['oldPassword'], $user->password)) {
            throw new BadRequestException('Старый пароль не совпадает');
        }

        $user->password = Hash::make($array['newPassword']);
        $user->save();
    }

}
