<?php

namespace App\Domains\Worker\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'age' => $this->age,
            'description' => $this->description,
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->path)),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}
