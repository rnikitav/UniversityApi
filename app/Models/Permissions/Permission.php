<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\Permission\Models\Permission as PermissionVendor;

/**
 * @package App\Models\Permissions
 * @property integer $id
 * @property string $name
 * @property string $guard_name
 * @property string $preview
 *
 * @method static $this first()
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class Permission extends PermissionVendor
{
    private static array $list = [
        'administrator' => ['preview' => 'Административный доступ', 'guard' => 'api'],
        'permissions.edit' => ['preview' => 'Редактирование ролей и разрешений', 'guard' => 'api'],
        'users.edit' => ['preview' => 'Редактирование пользователей', 'guard' => 'api'],
        'docs.view' => ['preview' => 'Просмотр документации API', 'guard' => 'web'],
        'news.edit' => ['preview' => 'Редактирование новостей', 'guard' => 'api'],
        'student' => ['preview' => 'Обучающийся', 'guard' => 'api'],
        'accelerators.edit' => ['preview' => 'Создание/редактирование акселераторов', 'guard' => 'api'],
        'expert' => ['preview' => 'Эксперт', 'guard' => 'api'],
        'image-collection.edit' => ['preview' => 'Редактирование коллекций изображений', 'guard' => 'api'],
        'tags.edit' => ['preview' => 'Редактирование тегов', 'guard' => 'api'],
    ];

    public static function getList(): array
    {
        return static::$list;
    }

    public static function getPermissionAdministrator(): string
    {
        return 'administrator';
    }

    public static function getPermissionStudent(): string
    {
        return 'student';
    }

    public static function getPermissionExpert(): string
    {
        return 'expert';
    }
}
