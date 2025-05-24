<?php

namespace App\Domains\Worker\Models;

use App\Domains\Image\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Worker extends Model
{
    /**
     * @OA\Schema(
     *     schema="Worker",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="firstname", type="string"),
     *     @OA\Property(property="lastname", type="string"),
     *     @OA\Property(property="age", type="int"),
     *     @OA\Property(property="description", type="string"),
     *     @OA\Property(
     *          property="images",
     *          type="array",
     *          @OA\Items(ref="#/components/schemas/Image")
     *      )
     * )
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'age',
        'description',
        'created_at',
        'updated_at'
    ];

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
