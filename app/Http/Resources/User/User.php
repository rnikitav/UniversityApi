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
            'email' => $this->resource->email,
            'roles' => RoleResource::collection($this->getRoles()),
        ];
    }

    private function getRoles(): Collection
    {
        if ($this->resource->roles->count()) {
            return $this->resource->roles;
        }

        if ($this->resource->hasPermissionTo('administrator')) {
            return collect([(object)[
                'id' => null,
                'name' => '_system',
                'permissions' => [Permission::findByName('administrator')]
            ]]);
        }

        return new Collection();
    }
}
