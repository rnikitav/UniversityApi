<?php

namespace App\Http\Resources\Accelerator;

use App\DTO\AcceleratorCaseCompleted as AcceleratorCaseCompletedDTO;
use App\Http\Resources\File\File as FileResource;
use App\Utils\Resource as ResourceHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseCompletedDTO $resource
 */
class AcceleratorCaseCompleted extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->model->id,
            'name' => $this->resource->model->name,
            'description' => $this->resource->model->description,
            'published_at' => ResourceHelpers::formatDateTime($this->resource->model->published_at),
            'average_score_experts' => $this->resource->model->scores->average('score'),
            'average_score_points' => $this->resource->model->solutions->average('score'),
            'file' => new FileResource($this->resource->file),
        ];
    }
}
