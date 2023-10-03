<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\User\UserShort as UserShortResource;
use App\Http\Resources\BaseSimple as BaseSimpleResource;
use App\Models\Accelerator\Case\AcceleratorCaseEvent as AcceleratorCaseEventModel;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseEventModel $resource
 */
class Event extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'initializer' => new UserShortResource($this->resource->initializer),
            'type' => new BaseSimpleResource($this->resource->type),
            'description' => $this->resource->description,
            'participant' => new UserShortResource($this->resource->participant),
            'status' => new BaseSimpleResource($this->resource->status),
            'moderator' => new UserShortResource($this->resource->moderator),
            'created_at' => ResourceHelpers::formatDateTime($this->resource->created_at),
            'updated_at' => ResourceHelpers::formatDateTime($this->resource->updated_at),
        ];
    }
}
