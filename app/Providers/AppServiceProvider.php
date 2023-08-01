<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        Passport::ignoreRoutes();
    }

    public function boot()
    {
        //
    }
}
