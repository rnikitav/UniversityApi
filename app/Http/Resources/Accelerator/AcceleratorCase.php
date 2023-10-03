<?php

namespace App\Http\Resources\Accelerator;

use App\Http\Resources\File\File as FileResource;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Http\Resources\Accelerator\CaseStatus as AcceleratorCaseStatusResource;
use App\Http\Resources\Accelerator\AcceleratorCaseParticipant as AcceleratorCaseParticipantResource;
use App\Http\Resources\Accelerator\CaseMessage as AcceleratorCaseMessageResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @package App\Http\Resources
 * @property AcceleratorCaseModel $resource
 */
class AcceleratorCase extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'status' => new AcceleratorCaseStatusResource($this->resource->status),
            'participants' => AcceleratorCaseParticipantResource::collection($this->resource->participants),
            'attachments' => FileResource::collection($this->resource->files),
            'messages' => AcceleratorCaseMessageResource::collection($this->resource->messages),
        ];
    }
}
