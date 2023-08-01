<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage, string $sort = 'id', string $cursor = null): CursorPaginator;

    public function byId(int $id): ?Model;

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function byIdOr404(int $id): Model;

    public function byIds(array $ids): Collection;
}
