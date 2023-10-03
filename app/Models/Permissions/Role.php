<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\Permission\Models\Role as RoleVendor;

/**
 * @package App\Models\Permissions
 * @property integer $id
 * @property string $name
 * @property string $guard_name
 * @property Collection $permissions
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class Role extends RoleVendor
{
    use HasFactory;

    protected $fillable = ['name', 'guard_name'];
}
