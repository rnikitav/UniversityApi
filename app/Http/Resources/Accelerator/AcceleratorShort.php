<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\BaseSimple as BaseSimpleResource;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorModel $resource
 */
class AcceleratorShort extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => new BaseSimpleResource($this->resource->status),
        ];
    }
}
