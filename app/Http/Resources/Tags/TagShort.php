<?php

namespace App\Http\Resources\Tags;

use App\Models\Tags\Tag as TagModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property TagModel $resource
 */
class TagShort extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
