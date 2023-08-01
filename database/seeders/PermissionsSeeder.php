<?php

namespace Database\Seeders;

use App\Models\Permissions\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsList = Permission::getList();
        $permissions = Permission::all(['name']);
        foreach ($permissionsList as $needle => $data) {
            if (!$permissions->contains('name', $needle)) {
                Permission::create([
                    'name' => $needle,
                    'preview' => $data['preview'],
                    'guard_name' => $data['guard']]
                );
            }
        }
    }
}
