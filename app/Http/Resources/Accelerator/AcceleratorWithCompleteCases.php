<?php

namespace App\Http\Resources\Accelerator;

use App\DTO\Accelerator as AcceleratorDTO;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorDTO $resource
 */
class AcceleratorWithCompleteCases extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->model->id,
            'name' => $this->resource->model->name,
            'cases' => AcceleratorCaseCompleted::collection($this->resource->cases),
        ];
    }
}
