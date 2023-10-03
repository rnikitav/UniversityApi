<?php

namespace Database\Seeders;

use App\Models\Permissions\Permission;
use App\Models\User\User;
use Database\Factories\User\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        if (!User::all()->count()) {
            /** @var UserFactory $userFactory */
            $userFactory = User::factory();
            $userAdmin = $userFactory->create([
                'login' => 'admin@admin.ru',
                'password' => Hash::make('admin')
            ]);
            $userAdmin->givePermissionTo('administrator', Permission::findByName('docs.view', 'web'));
        }
    }
}
