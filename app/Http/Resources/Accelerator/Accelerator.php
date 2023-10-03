<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Http\Resources\Accelerator\Status as AcceleratorStatusResource;
use App\Http\Resources\Accelerator\ControlPoint as AcceleratorControlPointResource;
use App\Http\Resources\File\File as FileResource;
use App\Utils\Resource as ResourceHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorModel $resource
 */
class Accelerator extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'published_at' => ResourceHelpers::formatDate($this->resource->published_at),
            'date_end_accepting' => ResourceHelpers::formatDate($this->resource->date_end_accepting),
            'date_end' => ResourceHelpers::formatDate($this->resource->date_end),
            'status' => new AcceleratorStatusResource($this->resource->status),
            'attachments' => FileResource::collection($this->resource->files),
            'control_points' => AcceleratorControlPointResource::collection($this->resource->controlPoints),
        ];
    }
}
