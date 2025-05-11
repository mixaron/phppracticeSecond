<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRequest extends Model
    /**
     * @OA\Schema(
     *     schema="UserRequest",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="status", type="enum"),
     *     @OA\Property(property="user_id", type="int"),
     *     @OA\Property(property="service_id", type="int")
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
}
