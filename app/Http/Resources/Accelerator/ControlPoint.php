<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;

/**
 * @package App\Http\Resources
 * @property AcceleratorControlPointModel $resource
 */
class ControlPoint extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'date_completion' => ResourceHelpers::formatDate($this->resource->date_completion),
            'max_score' => $this->resource->max_score,
        ];
    }
}
