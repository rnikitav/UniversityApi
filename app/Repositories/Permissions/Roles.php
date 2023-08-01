<?php

namespace App\Repositories\Permissions;

use App\Models\Permissions\Role;
use App\Repositories\AbstractRepository;

class Roles extends AbstractRepository
{
    protected function getClassName(): string
    {
        return Role::class;
    }
}
