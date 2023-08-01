<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function response;

class UserController extends Controller
{
    public function me(Request $request): Response
    {
        return response(new UserResource($request->user()));
    }
}
