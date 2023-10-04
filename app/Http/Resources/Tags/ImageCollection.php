<?php

namespace App\Http\Resources\Tags;

use App\Http\Resources\File\File as FileResource;
use App\Models\Tags\ImageCollection as ImageCollectionModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property ImageCollectionModel $resource
 */
class ImageCollection extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'attachments' => FileResource::collection($this->resource->files),
        ];
    }
}
