<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\User\UserShort as UserShortResource;
use App\Models\Accelerator\Case\AcceleratorCaseMessage as AcceleratorCaseMessageModel;
use App\Utils\Resource as ResourceHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseMessageModel $resource
 */
class CaseMessage extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => new UserShortResource($this->resource->user),
            'message' => $this->resource->message,
            'at' => ResourceHelpers::formatDateTime($this->resource->created_at),
        ];
    }
}
