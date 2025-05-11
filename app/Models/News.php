<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
    /**
     * @OA\Schema(
     *     schema="News",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string")
     *     @OA\Property(property="category_id", type="integer")
     *
     * )
     */
{
    protected $fillable = [
        'title',
        'description',
//        'image',
        'category_id',
        'created_at',
        'updated_at'
    ];

    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
}
