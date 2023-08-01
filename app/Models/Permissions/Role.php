<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role as RoleVendor;

/**
 * @package App\Models\Permissions
 * @property integer $id
 * @property string $name
 * @property string $guard_name
 * @property Collection $permissions
 *
 * @method static $this create(array $attributes = [])
 */
class Role extends RoleVendor
{
    protected $fillable = ['name', 'guard_name'];
}
