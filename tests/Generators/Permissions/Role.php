<?php

namespace Tests\Generators\Permissions;

use App\Models\Permissions\Role as RoleModel;
use Database\Factories\Permissions\RoleFactory;
use Illuminate\Database\Eloquent\Collection;

class Role
{
    protected static function getBaseFactory(int $count = null, array $data = []): RoleFactory
    {
        /** @var RoleFactory $factory */
        $factory = RoleModel::factory($count);
        if (!$data) {
            $factory = $factory->mock();
        } else {
            $factory = $factory->state($data);
        }
        return $factory;
    }

    public static function create(int $count = null, array $data = []): RoleModel | Collection
    {
        return static::getBaseFactory($count, $data)->create();
    }
}
