<?php

namespace App\Http\Resources\User;

use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseModel $resource
 */
class Favorite extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'accelerator' => $this->resource->accelerator->name,
        ];
    }
}
