<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCategory extends Model
{
    /**
     * @OA\Schema(
     *     schema="ServiceCategory",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string")
     *
     * )
     */
    protected $table = 'service_category';

    protected $fillable = [
        'title',
        'description',
//        'image',
        'created_at',
        'updated_at'
    ];

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
