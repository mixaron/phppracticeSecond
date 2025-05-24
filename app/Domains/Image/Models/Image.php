<?php

namespace App\Domains\Image\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    /**
     * @OA\Schema(
     *     schema="Image",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="url", type="string", format="url"),
     *     @OA\Property(property="imageable_id", type="integer"),
     *     @OA\Property(property="imageable_type", type="string")
     * )
     */

    protected $fillable = ['path'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
