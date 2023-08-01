<?php

namespace App\Http\Resources\User;

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
            'email' => $this->resource->email,
        ];
    }
}
