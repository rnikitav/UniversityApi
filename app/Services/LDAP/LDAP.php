<?php

namespace App\Services\LDAP;

use App\Services\LDAP\Contracts\LDAPService;
use Laravel\Passport\Bridge\User as UserEntity;

class LDAP extends LDAPAbstract implements LDAPService
{

    public function authenticate(string $username, string $password): ?UserEntity
    {
        return new UserEntity(1);
    }
}
