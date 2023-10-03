<?php

namespace App\Services\LDAP;

use App\Services\LDAP\Contracts\LDAPService;
use Illuminate\Support\Str;
use Laravel\Passport\Bridge\User as UserEntity;

class LDAPMock extends LDAPAbstract implements LDAPService
{
    protected static array $mockData = [
        'login' => 'ldaptest%s',
        'email' => 'ldaptest%s@test.ru',
    ];

    public function authenticate(string $username, string $password): ?UserEntity
    {
        if (!Str::startsWith($username, 'ldaptest')) {
            return null;
        }

        $postfix = preg_replace('/^ldaptest/', '', $username);

        $data = $this->makeMockData($postfix);
        $user = $this->getOrCreateUser($data);

        return new UserEntity($user->id);
    }

    protected function makeMockData(string $postfix): array
    {
        return array_reduce(array_keys(static::$mockData), function (array $initial, string $key) use ($postfix) {
            $initial[$key] = sprintf(static::$mockData[$key], $postfix);
            return $initial;
        }, []);
    }
}
