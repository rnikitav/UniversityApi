<?php

namespace App\Http\Resources\User;

use App\Models\User\UserMainData;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;

/**
 * @package App\Http\Resources
 * @property UserMainData $resource
 */
class MainData extends JsonResource
{
    public function toArray($request): array
    {
        return ResourceHelpers::fromFillable($this->resource);
    }
}
