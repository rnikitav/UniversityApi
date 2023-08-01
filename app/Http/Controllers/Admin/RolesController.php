<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\RolesAdd as RolesAddRequest ;
use App\Http\Requests\Permissions\RolesEdit as RolesEditRequest;
use App\Http\Resources\Permission\Role as RoleResource;
use App\Models\Permissions\Role;
use App\Repositories\Permissions\Roles as RolesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class RolesController extends Controller
{
    protected RolesRepository $rolesRepository;

    public function __construct(RolesRepository $rolesRepository, Request $request)
    {
        $this->rolesRepository = $rolesRepository;
        if (Arr::exists(['PATCH','POST','DELETE'], $request->method())) {
            $this->middleware('permission:permissions.edit');
        } else {
            $this->middleware('permission:permissions.edit|users.edit');
        }
    }

    public function index(): Response
    {
        $items = $this->rolesRepository->all();
        return response(RoleResource::collection($items));
    }

    public function store(RolesAddRequest $request): Response
    {
        $validatedData = $request->all();

        $new = Role::create(['name' => $validatedData['name']]);
        $this->syncPermissions($new, $validatedData);

        return response(new RoleResource($new));
    }

    public function show(int $id): Response
    {
        $item = $this->rolesRepository->byIdOr404($id);
        return response(new RoleResource($item));
    }

    public function update(RolesEditRequest $request, int $id): Response
    {
        $validatedData = $request->all();
        /** @var Role $item */
        $item = $this->rolesRepository->byIdOr404($id);
        $item->update($validatedData);
        $this->syncPermissions($item, $validatedData);

        return response(new RoleResource($item));
    }

    public function destroy(int $id): Response
    {
        $item = $this->rolesRepository->byIdOr404($id);
        $item->delete();

        return response(['status' => true]);
    }

    /**
     * Синхронизация разрешений у роли
     * @param Role $role
     * @param array $requestData
     */
    protected function syncPermissions(Role $role, array $requestData)
    {
        if (array_key_exists('permissions', $requestData)) {
            $permissions = Arr::pluck($requestData['permissions'], 'name');
            $role->syncPermissions($permissions);
        }
    }
}
