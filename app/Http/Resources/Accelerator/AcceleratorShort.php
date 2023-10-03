<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Http\Resources\Accelerator\Status as AcceleratorStatusResource;
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
            'status' => new AcceleratorStatusResource($this->resource->status),
        ];
    }
}