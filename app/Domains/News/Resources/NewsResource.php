<?php

namespace App\Domains\News\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => [
                'id' => $this->newsCategory->id,
                'title' => $this->newsCategory->title,
            ],
            'images' => $this->images->map(fn($img) => asset('storage/' . $img->path)),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}

