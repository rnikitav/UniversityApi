<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthUserNotVerifiedException;
use App\Repositories\User\User as UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->isOk()) {
            $user = (new UserRepository())->byEmail($request->input('username') ?? '');
            if ($user && !$user->hasVerifiedEmail()) {
                throw new AuthUserNotVerifiedException($request->input('username'));
            }
        }

        return $response;
    }
}
