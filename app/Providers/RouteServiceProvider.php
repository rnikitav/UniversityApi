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
    protected const ID_PATTERN = '^[1-9][0-9]*$';

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

        Route::patterns([
            'id' => self::ID_PATTERN,
            'user_id' => self::ID_PATTERN,
            'case_id' => self::ID_PATTERN,
            'event_id' => self::ID_PATTERN,
        ]);
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
