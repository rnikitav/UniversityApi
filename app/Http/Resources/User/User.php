<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Permission\Role as RoleResource;
use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Models\Permissions\Permission as PermissionModel;
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

        $permissionAdministrator = PermissionModel::getPermissionAdministrator();
        if ($this->resource->hasPermissionTo($permissionAdministrator)) {
            $systemRole['permissions'][] = Permission::findByName($permissionAdministrator);
        }

        $permissionStudent = PermissionModel::getPermissionStudent();
        if ($this->resource->hasPermissionTo($permissionStudent)) {
            $systemRole['permissions'][] = Permission::findByName($permissionStudent);
        }

        if (count($systemRole['permissions'])) {
            $roles->push((object)$systemRole);
        }

        return $roles;
    }
}
