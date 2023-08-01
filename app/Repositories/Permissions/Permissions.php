<?php

namespace App\Repositories\Permissions;

use App\Models\Permissions\Permission;
use App\Repositories\AbstractRepository;

class Permissions extends AbstractRepository
{
    protected function getClassName(): string
    {
        return Permission::class;
    }
}
