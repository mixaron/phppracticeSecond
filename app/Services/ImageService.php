<?php

namespace App\Services;

use App\Domains\News\Models\News;
use App\Domains\Service\Models\Service;
use App\Domains\Worker\Models\Worker;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function __construct()
    {
    }

    public function addImages(array $images, Service|News|Worker $model): void
    {
        foreach ($images as $file) {
            $path = $file->store('uploads', 'public');
            $model->images()->create(['path' => $path]);
        }
    }

    public function updateImages(array $images, Service|News|Worker $model): void
    {
        $this->deleteImages($model);


        foreach ($images as $file) {
            $path = $file->store('uploads', 'public');
            $model->images()->create(['path' => $path]);
        }
    }

    public function deleteImages(Service|News|Worker $model): void
    {
        foreach ($model->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $model->images()->delete();
    }
}
