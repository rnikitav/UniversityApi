<?php

namespace App\Http\Resources\File;

use App\Models\File as FileModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @package App\Http\Resources
 * @property FileModel $resource
 */
class File extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'category' => $this->resource->category,
            'path' => $this->getPath(),
            'original_name' => $this->resource->original_name
        ];
    }

    protected function getPath(): string
    {
        return $this->resource->disk == 'public'
            ? Storage::disk('public')->url($this->resource->path)
            : sprintf('/api/file/%1$s/%2$s', $this->resource->id, $this->resource->sha256);
    }
}
