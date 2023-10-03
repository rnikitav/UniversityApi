<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Http\Resources\Accelerator\CaseStatus as AcceleratorCaseStatusResource;
use App\Http\Resources\Accelerator\CaseParticipation as AcceleratorCaseParticipationResource;
use App\Http\Resources\User\UserShort as UserShortResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseModel $resource
 */
class AcceleratorCaseShort extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => new AcceleratorCaseStatusResource($this->resource->status),
            'participation' => new AcceleratorCaseParticipationResource($this->resource->participation),
            'owner' => new UserShortResource($this->resource->owner?->user),
        ];
    }
}
