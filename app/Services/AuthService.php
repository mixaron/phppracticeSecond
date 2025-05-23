<?php

namespace App\Services;

use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function generateToken(User $user): string
    {
        return Auth::login($user);
    }

    public function attemptLogin(array $credentials): ?string
    {
        return Auth::attempt($credentials) ?: null;
    }
}
