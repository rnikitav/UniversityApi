<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCaseStatus as AcceleratorCaseStatusModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseStatusModel $resource
 */
class CaseStatus extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
