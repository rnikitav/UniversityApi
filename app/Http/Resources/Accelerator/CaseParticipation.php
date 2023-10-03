<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCaseParticipation as AcceleratorCaseParticipationModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseParticipationModel $resource
 */
class CaseParticipation extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
