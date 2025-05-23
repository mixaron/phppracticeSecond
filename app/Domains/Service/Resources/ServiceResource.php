<?php

namespace App\Domains\Service\Resources;

use App\Domains\Review\Resources\ReviewResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category' => [
                'id' => $this->serviceCategory->id,
                'title' => $this->serviceCategory->title,
            ],
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->path)),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}
