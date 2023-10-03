<?php

namespace App\Http\Resources\News;

use App\Http\Resources\File\File as FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Utils\Resource as ResourceHelpers;
use App\Models\News\News as NewsModel;

/**
 * @package App\Http\Resources
 * @property NewsModel $resource
 */
class NewsResource extends JsonResource
{

    public function toArray($request):array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'body' => $this->resource->body,
            'published_at' => ResourceHelpers::formatDate($this->resource->published_at),
            'created_at' => ResourceHelpers::formatDate($this->resource->created_at),
            'updated_at' => ResourceHelpers::formatDate($this->resource->updated_at),
            'files' => FileResource::collection($this->resource->files),
        ];
    }
}
