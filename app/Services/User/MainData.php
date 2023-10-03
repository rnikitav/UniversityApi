<?php

namespace App\Services\User;

use App\Models\User\User as UserModel;

class MainData
{
    public static function update(UserModel $user): void
    {
        if (is_null($user->mainData)) {
            $user->mainData()->create($user->mainDataForUpdate ?? []);
        } else {
            $user->mainData()->update($user->mainDataForUpdate ?? []);
        }
    }
}
