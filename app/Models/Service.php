<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
    /**
     * @OA\Schema(
     *     schema="Service",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="desciprion", type="string"),
     *     @OA\Property(property="price", type="decimal")
     *
     * )
     */
{
    protected $fillable = [
        'title',
        'description',
//        'image',
        'price',
        'created_at',
        'updated_at'
    ];

    public function serviceRequests()
    {
        return $this->hasMany(UserRequest::class);
    }
}
