<?php

namespace App\Services\LDAP\Contracts;

use League\OAuth2\Server\Entities\UserEntityInterface;

interface LDAPService
{
    public function authenticate(string $username, string $password): ?UserEntityInterface;
}
