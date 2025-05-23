<?php

namespace App\Domains\NewsCategory\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    /**
     * @OA\Schema(
     *     schema="NewsCategory",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string")
     *
     * )
     */

    protected $table = 'news_category';

    protected $fillable = [
        'title',
        'description',
        'created_at',
        'updated_at'
    ];
}
