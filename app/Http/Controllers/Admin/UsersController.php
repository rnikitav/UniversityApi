<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Create as UserCreateRequest;
use App\Http\Requests\User\Update as UserUpdateRequest;
use App\Http\Resources\User\User as UserResource;
use App\Http\Resources\User\UserShortCollection;
use App\Models\User\User as UserModel;
use App\Repositories\User\User as UserRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Throwable;

class UsersController extends Controller
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('permission:users.edit');
        $this->userRepository = $userRepository;
    }

    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->userRepository->paginate($perPage)
            : $this->userRepository->all();

        return response(new UserShortCollection($items));
    }

    /**
     * @throws Throwable
     */
    public function store(UserCreateRequest $request): Response
    {

        $data = $request->prepareDataForCreateUser();

        $new = DBUtils::inTransaction(function () use ($data) {
            return UserModel::factory()->create($data);
        });

        return response(new UserResource($new->refresh()));
    }

    public function show(int $id): Response
    {
        $item = $this->userRepository->byIdOr404($id);
        return response(new UserResource($item));
    }

    /**
     * @throws Throwable
     */
    public function update(UserUpdateRequest $request, int $id): Response
    {
        /** @var UserModel $user */
        $user = $this->userRepository->byIdOr404($id);
        $data = $request->prepareDataForCreateUser();

        if ($user->external) {
            Arr::forget($data, ['login', 'password']);
        }

        DBUtils::inTransaction(function () use ($data, $user) {
            $user->update($data);
        });

        return response(new UserResource($user->refresh()));
    }

    public function destroy(int $id): Response
    {
        $item = $this->userRepository->byIdOr404($id);
        $item->delete();

        return response(['status' => true]);
    }
}
