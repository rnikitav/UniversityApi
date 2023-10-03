<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

class NewsCollectionResource extends ResourceCollection
{

    public function toArray($request): array
    {
        $data = [
            'data' => $this->collection
        ];
        $cursor = $this->resource instanceof CursorPaginator
            ? ['cursor' => $this->resource->nextCursor()?->encode()]
            :[];
        return array_merge($data, $cursor);

    }
}
