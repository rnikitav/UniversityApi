<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\CursorPaginator;

abstract class AbstractRepository implements BaseRepositoryInterface
{
    protected string $className;
    protected Model $model;

    public function __construct()
    {
        $this->className = $this->getClassName();
        $this->model = App::make($this->className);
    }

    abstract protected function getClassName(): string;

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage, string $sort = 'id', string $cursor = null): CursorPaginator
    {
        return $this->model->orderBy($sort)->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function byId(int $id): ?Model
    {
        return $this->model->where('id', $id)->get()->first();
    }

    public function byIdOr404(int $id): Model
    {
        $find = $this->byId($id);
        if (!$find) {
            abort(404);
        }
        return $find;
    }

    public function byIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}
