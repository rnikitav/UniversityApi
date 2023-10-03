<?php

namespace App\Services\LDAP\Grant;

use App\Services\LDAP\Contracts\LDAPService as LDAPServiceInterface;
use Illuminate\Support\Facades\App;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;

class PasswordActiveDirectoryGrant extends PasswordGrant
{
    /**
     * @throws OAuthServerException
     * @return UserEntityInterface
     */
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $username = $this->getRequestParameter('username', $request);

        if (!\is_string($username)) {
            throw OAuthServerException::invalidRequest('username');
        }

        $password = $this->getRequestParameter('password', $request);

        if (!\is_string($password)) {
            throw OAuthServerException::invalidRequest('password');
        }

        $LDAPService = App::make(LDAPServiceInterface::class);
        $user = $LDAPService->authenticate($username, $password);

        if (is_null($user)) {
            throw OAuthServerException::invalidCredentials();
        }

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    public function getIdentifier()
    {
        return 'password_active_directory';
    }
}
