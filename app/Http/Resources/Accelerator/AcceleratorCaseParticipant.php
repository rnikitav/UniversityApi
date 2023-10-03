<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\BaseSimple as BaseSimpleResource;
use App\Models\Accelerator\Case\AcceleratorCaseParticipant as AcceleratorCaseParticipantModel;
use App\Http\Resources\User\UserShort as UserShortResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseParticipantModel $resource
 */
class AcceleratorCaseParticipant extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => new UserShortResource($this->resource->user),
            'role' => new BaseSimpleResource($this->resource->role),
        ];
    }
}
