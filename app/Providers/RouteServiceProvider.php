<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/docs/request-docs';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api/user.php'))
                ->group(base_path('routes/api/api.php'))
                ->group(base_path('routes/api/admin.php'))
                ->group(base_path('routes/api/auth.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::pattern('user_id', '^[1-9][0-9]*');
        Route::pattern('id', '^[1-9][0-9]*');
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
