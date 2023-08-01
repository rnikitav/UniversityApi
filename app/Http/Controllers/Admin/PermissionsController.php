<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\PermissionEdit as PermissionEditRequest;
use App\Http\Resources\Permission\Permission as PermissionResource;
use App\Repositories\Permissions\Permissions as PermissionsRepository;
use Illuminate\Http\Response;

class PermissionsController extends Controller
{
    protected PermissionsRepository $permissionsRepository;

    public function __construct(PermissionsRepository $permissionsRepository)
    {
        $this->middleware('permission:permissions.edit');
        $this->permissionsRepository = $permissionsRepository;
    }

    public function index(): Response
    {
        $items = $this->permissionsRepository->all();
        return response(PermissionResource::collection($items));
    }

    public function show(int $id): Response
    {
        $item = $this->permissionsRepository->byIdOr404($id);
        return response(new PermissionResource($item));
    }

    public function update(PermissionEditRequest $request, int $id): Response
    {
        $validatedData = $request->all();
        if (array_key_exists('name', $validatedData)){
            unset($validatedData['name']);
        }
        $item = $this->permissionsRepository->byIdOr404($id);
        $item->update($validatedData);

        return response(new PermissionResource($item));
    }
}
