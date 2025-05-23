<?php

namespace App\Domains\User\Models;

use App\Domains\Review\Models\Review;
use App\Domains\Service\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRequest extends Model
    /**
     * @OA\Schema(
     *     schema="UserRequest",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="status", type="enum"),
     *     @OA\Property(property="user_id", type="integer"),
     *     @OA\Property(property="service_id", type="integer")
     *
     * )
     */
{
    protected $table = 'requests';

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'service_id',
        'created_at',
        'updated_at'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'request_id');
    }
}
