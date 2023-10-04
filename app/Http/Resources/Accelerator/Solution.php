<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\Accelerator\ControlPoint as ControlPointResource;
use App\Http\Resources\File\File as FileResource;
use App\Http\Resources\User\UserShort as UserShortResource;
use App\Http\Resources\BaseSimple as BaseSimpleResource;
use App\Models\Accelerator\Case\AcceleratorCaseSolution as AcceleratorCaseSolutionModel;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseSolutionModel $resource
 */
class Solution extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'control_point' => new ControlPointResource($this->resource->controlPoint),
            'author' => new UserShortResource($this->resource->author),
            'description' => $this->resource->description,
            'status' => new BaseSimpleResource($this->resource->status),
            'score' => $this->resource->score,
            'created_at' => ResourceHelpers::formatDateTime($this->resource->created_at),
            'updated_at' => ResourceHelpers::formatDateTime($this->resource->updated_at),
            'attachments' => FileResource::collection($this->resource->files),
        ];
    }
}
