<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use App\Models\News\News as NewsModel;

/**
 * @package App\Http\Resources
 * @property Collection $collection
 * @property CursorPaginator|Collection<NewsModel> $resource
 */
class NewsCollectionResource extends ResourceCollection
{

    public function toArray($request): array
    {
        $data = [
            'data' => NewsResource::collection($this->collection)
        ];
        $cursor = $this->resource instanceof CursorPaginator
            ? ['cursor' => $this->resource->nextCursor()?->encode()]
            :[];
        return array_merge($data, $cursor);

    }
}
