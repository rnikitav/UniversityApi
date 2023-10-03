<?php

namespace Tests\Generators;

use App\Models\User\User as UserModel;
use Database\Factories\User\UserFactory;
use Illuminate\Database\Eloquent\Collection;

class User
{
    protected static function getBaseFactory(int $count = null, array $data = []): UserFactory
    {
        /** @var UserFactory $factory */
        $factory = UserModel::factory($count);
        if (!$data) {
            $factory = $factory->mock();
        } else {
            $factory = $factory->state($data);
        }
        return $factory;
    }

    public static function createVerified(int $count = null, array $data = []): UserModel | Collection
    {
        return static::getBaseFactory($count, $data)->create();
    }

    public static function createUnVerified($count = null, array $data = []): UserModel | Collection
    {
        return static::getBaseFactory($count, $data)->unverified()->create();
    }

    public static function createExternal($count = null, array $data = []): UserModel | Collection
    {
        return static::getBaseFactory($count, $data)->external()->create();
    }
}
