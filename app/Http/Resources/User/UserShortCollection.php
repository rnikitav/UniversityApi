<?php

namespace App\Http\Resources\User;

use App\Models\User\User as UserModel;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

/**
 * @package App\Http\Resources
 * @property Collection $collection
 * @property CursorPaginator|Collection<UserModel> $resource
 */
class UserShortCollection extends ResourceCollection
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
