<?php

namespace App\Http\Resources\User;

use App\Http\Resources\User\MainData as MainDataResource;
use App\Models\User\User as UserModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property UserModel $resource
 */
class UserShort extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'main_data' => new MainDataResource($this->resource->mainData),
        ];
    }
}
