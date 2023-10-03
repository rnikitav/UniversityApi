<?php

namespace App\Providers;

use App\Models\User\User;
use App\Services\LDAP\Contracts\LDAPService;
use App\Services\LDAP\Grant\PasswordActiveDirectoryGrant;
use App\Services\LDAP\LDAP;
use App\Services\LDAP\LDAPMock;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function register()
    {
        parent::register();

        $this->app->bind(LDAPService::class, function (Container $app) {
            if (config('app.env') == 'production') {
                return $app->make(LDAP::class);
            }

            return $app->make(LDAPMock::class);
        });
    }

    public function boot(AuthorizationServer $authorizationServer)
    {
        Passport::tokensExpireIn(now()->addDay());
        Passport::refreshTokensExpireIn(now()->addDays(30));

        $authorizationServer->enableGrantType(
            $this->makePasswordActiveDirectoryGrant(), Passport::tokensExpireIn()
        );

        $this->registerPolicies();

        Gate::before(function (User $user) {
            return $user->hasPermissionTo('administrator') ? true : null;
        });
    }

    protected function makePasswordActiveDirectoryGrant(): PasswordActiveDirectoryGrant
    {
        $grant = new PasswordActiveDirectoryGrant(
            $this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
