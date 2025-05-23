<?php

namespace App\Domains\Review\Models;

use App\Domains\Service\Models\Service;
use App\Domains\User\Models\User;
use App\Domains\User\Models\UserRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Review extends Model
    /**
     * @OA\Schema(
     *     schema="Review",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="estimation", type="integer"),
     *     @OA\Property(property="request_id", type="integer")
     *
     * )
     */
{
    protected $table = 'reviews';

    protected $fillable = [
        'description',
        'estimation',
        'status',
        'request_id',
        'created_at',
        'updated_at'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(UserRequest::class, 'request_id');
    }

    public function service(): HasOneThrough
    {
        return $this->hasOneThrough(Service::class, UserRequest::class,
            'id', 'id', 'request_id', 'service_id');
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, UserRequest::class,
            'id', 'id', 'request_id', 'user_id');
    }

}
