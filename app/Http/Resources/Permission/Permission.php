<?php

namespace App\Http\Resources\Permission;

use App\Models\Permissions\Permission as PermissionModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property PermissionModel $resource
 */
class Permission extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'preview' => $this->resource->preview,
        ];
    }
}
