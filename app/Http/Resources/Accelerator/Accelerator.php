<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Http\Resources\Accelerator\AcceleratorStatus as AcceleratorStatusResource;
use App\Http\Resources\File\File as FileResource;
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
            'published_at' => $this->resource->published_at?->format('Y-m-d'),
            'date_end_accepting' => $this->resource->date_end_accepting?->format('Y-m-d'),
            'date_end' => $this->resource->date_end?->format('Y-m-d'),
            'status' => new AcceleratorStatusResource($this->resource->status),
            'attachments' => FileResource::collection($this->resource->files)
        ];
    }
}
