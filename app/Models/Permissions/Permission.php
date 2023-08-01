<?php

namespace App\Models\Permissions;

use Spatie\Permission\Models\Permission as PermissionVendor;

/**
 * @package App\Models\Permissions
 * @property integer $id
 * @property string $name
 * @property string $guard_name
 * @property string $preview
 */
class Permission extends PermissionVendor
{
    private static array $list = [
        'administrator' => ['preview' => 'Административный доступ', 'guard' => 'api'],
        'permissions.edit' => ['preview' => 'Редактирование ролей и разрешений', 'guard' => 'api'],
        'users.edit' => ['preview' => 'Редактирование пользователей', 'guard' => 'api'],
        'docs.view' => ['preview' => 'Просмотр документации API', 'guard' => 'web'],
    ];

    public static function getList(): array
    {
        return static::$list;
    }
}
