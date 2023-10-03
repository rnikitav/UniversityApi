<?php

namespace App\Http\Resources\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCaseRole as AcceleratorCaseRoleModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseRoleModel $resource
 */
class CaseRole extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
