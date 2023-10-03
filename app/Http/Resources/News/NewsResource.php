<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;

class NewsResource extends JsonResource
{

    public function toArray($request):array
    {
        $notFillable = [
            'id' => $this->resource->id,
            'created_at' => $this->resource->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at->format('Y-m-d H:i:s'),
        ];
        return array_merge($notFillable, ResourceHelpers::fromFillable($this->resource));
    }
}
