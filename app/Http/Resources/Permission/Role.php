<?php

namespace App\Http\Resources\Permission;

use App\Models\Permissions\Role as RoleModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property RoleModel $resource
 */
class Role extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'permissions' => Permission::collection($this->resource->permissions)
        ];
    }
}
