<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
    /**
     * @OA\Schema(
     *     schema="Contacts",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="address", type="string"),
     *     @OA\Property(property="phone", type="string"),
     *     @OA\Property(property="email", type="email")
     *     @OA\Property(property="work_time", type="string")
     * )
     */
{
    protected $fillable = [
        'address',
        'phone',
        'email',
        'work_time',
        'created_at',
        'updated_at'
    ];
}
