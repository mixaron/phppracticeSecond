<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
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

}
