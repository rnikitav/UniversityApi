<?php

namespace App\Http\Controllers\Admin;

use App\Factories\User as UserFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Create as UserCreateRequest;
use App\Http\Requests\User\Update as UserUpdateRequest;
use App\Http\Resources\User\User as UserResource;
use App\Http\Resources\User\UserShortCollection;
use App\Models\User\User;
use App\Repositories\User\User as UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

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

    public function store(UserCreateRequest $request): Response
    {
        $new = UserFactory::fromCreateRequest($request);
        $this->syncRoles($new, $request->all());

        return response(new UserResource($new));
    }

    public function show(int $id): Response
    {
        $item = $this->userRepository->byIdOr404($id);
        return response(new UserResource($item));
    }

    public function update(UserUpdateRequest $request, int $id): Response
    {
        $validatedData = $request->all();
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        /** @var User $item */
        $item = $this->userRepository->byIdOr404($id);
        $item->update($validatedData);
        $this->syncRoles($item, $validatedData);

        return response(new UserResource($item));
    }

    public function destroy(int $id): Response
    {
        $item = $this->userRepository->byIdOr404($id);
        $item->delete();

        return response(['status' => true]);
    }

    /**
     * Синхронизация ролей пользователя
     * @param User $user
     * @param array $requestData
     */
    protected function syncRoles(User $user, array $requestData)
    {
        if (array_key_exists('roles', $requestData)) {
            $roles = Arr::pluck($requestData['roles'], 'id');
            $user->syncRoles($roles);
        }
    }
}
