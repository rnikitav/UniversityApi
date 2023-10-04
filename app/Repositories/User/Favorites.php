<?php

namespace App\Repositories\User;

use App\Models\Accelerator\Case\AcceleratorCase;
use App\Models\User\User as UserModel;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Favorites implements BaseRepositoryInterface
{
    protected ?UserModel $currentUser;

    public function __construct(Request $request)
    {
        $this->currentUser = $request->user();
    }

    public function all(): Collection
    {
        return $this->currentUser->favoriteCases;
    }

    public function paginate(int $perPage, string $sort = 'id', string $cursor = null): CursorPaginator
    {
        return $this->currentUser->favoriteCases()->orderBy($sort)->paginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function byId(int $id): ?AcceleratorCase
    {
        return $this->currentUser->favoriteCases()->first($id);
    }

    public function byIdOr404(int $id): AcceleratorCase
    {
        $find = $this->byId($id);
        if (!$find) {
            abort(404);
        }
        return $find;
    }

    public function byIds(array $ids): Collection
    {
        return $this->currentUser->favoriteCases()->whereIn('id', $ids)->get();
    }
}
