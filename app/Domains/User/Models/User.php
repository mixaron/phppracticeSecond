<?php

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
    /**
     * @OA\Schema(
     *     schema="User",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="firstname", type="string"),
     *     @OA\Property(property="lastname", type="string"),
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="phone", type="string"),
     *     @OA\Property(property="role", type="string")
     *
     * )
     */
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'role',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function userRequests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims(): array
    { return []; }

    public function is_admin(): bool
    {
        return $this->role === 'admin';
    }
}
