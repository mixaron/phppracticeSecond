<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function count(): int
    {
        return User::count();
    }

    public function findAll(): Collection
    {
        return User::all();
    }

    public function deleteById(string $id): void
    {
        User::destroy($id);
    }

}
