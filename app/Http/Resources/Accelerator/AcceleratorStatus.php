<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\AcceleratorStatus as AcceleratorStatusModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorStatusModel $resource
 */
class AcceleratorStatus extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
