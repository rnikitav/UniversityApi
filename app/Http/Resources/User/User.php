<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Permission\Role as RoleResource;
use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @package App\Http\Resources
 * @property UserModel $resource
 */
class User extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'roles' => RoleResource::collection($this->getRoles()),
        ];
    }

    private function getRoles(): Collection
    {
        $roles = $this->resource->roles->count() ? $this->resource->roles : new Collection();
        $systemRole = [
            'id' => null,
            'name' => '_system',
            'permissions' => []
        ];

        if ($this->resource->hasPermissionTo('administrator')) {
            $systemRole['permissions'][] = Permission::findByName('administrator');
        }

        if ($this->resource->hasPermissionTo('student')) {
            $systemRole['permissions'][] = Permission::findByName('student');
        }

        if (count($systemRole['permissions'])) {
            $roles->push((object)$systemRole);
        }

        return $roles;
    }
}
