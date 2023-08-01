<?php

namespace App\Http\Middleware;

use App\Exceptions\OperationNotPermittedException;
use App\Models\User\User;
use Closure;
use Illuminate\Support\Facades\Route;

class ChangeUserData
{
    public function handle($request, Closure $next)
    {
        $userDd = Route::current()->parameter('user_id');

        /** @var User $currentUser */
        $currentUser = $request->user();

        if ($currentUser->id != $userDd && !$currentUser->hasAnyPermission('user.edit', 'administrator')) {
            throw new OperationNotPermittedException();
        }

        return $next($request);
    }
}
