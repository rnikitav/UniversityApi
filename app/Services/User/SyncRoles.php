<?php

namespace App\Services\User;

use App\Models\User\User as UserModel;

class SyncRoles
{
    public static function update(UserModel $user): void
    {
        if (!is_null($user->rolesForUpdate)) {
            $user->syncRoles($user->rolesForUpdate);
        }
    }
}
