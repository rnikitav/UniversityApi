<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Http\Request;

use Closure;

class AcceptJson extends TransformsRequest
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
        if ($request->is('api/*')) {
            $accept = $request->header('accept');
            if (!$accept || $accept == '*/*') {
                $request->headers->set('accept', 'application/json');
            }
        }

        return parent::handle($request, $next);
    }
}
