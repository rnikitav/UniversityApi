<?php

namespace App\Http\Resources\Tags;

use App\Models\Tags\ImageCollection as ImageCollectionModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property ImageCollectionModel $resource
 */
class ImageCollectionShort extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
