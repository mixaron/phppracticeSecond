<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
    /**
     * @OA\Schema(
     *     schema="Service",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="category_id", type="integer")
     *     @OA\Property(property="price", type="decimal")
     *
     * )
     */
{
    protected $fillable = [
        'title',
        'description',
//        'image',
        'category_id',
        'price',
        'created_at',
        'updated_at'
    ];

    public function serviceRequests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
