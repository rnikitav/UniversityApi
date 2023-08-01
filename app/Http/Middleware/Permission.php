<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middlewares\PermissionMiddleware;

class Permission extends PermissionMiddleware
{
    /**
     * Обертка над PermissionMiddleware для перевода фраз
     */
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        try {
            return parent::handle($request, $next, $permission, $guard);
        } catch (UnauthorizedException $e) {
            throw new HttpException(403, __($e->getMessage()));
        }
    }
}
