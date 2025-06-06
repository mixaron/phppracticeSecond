<?php

namespace App\Domains\News\Models;

use App\Domains\Image\Models\Image;
use App\Domains\NewsCategory\Models\NewsCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
    /**
     * @OA\Schema(
     *     schema="News",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="category_id", type="integer"),
     *     @OA\Property(
     *          property="images",
     *          type="array",
     *          @OA\Items(ref="#/components/schemas/Image")
     *      )
     * )
     */
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'created_at',
        'updated_at'
    ];

    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
