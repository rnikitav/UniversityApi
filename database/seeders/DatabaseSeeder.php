<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsSeeder::class,
            UserSeeder::class,
            AcceleratorStatusSeeder::class,
            AcceleratorCaseStatusSeeder::class,
            AcceleratorCaseRoleSeeder::class,
            AcceleratorCaseParticipationSeeder::class,
            AcceleratorCaseEventStatusSeeder::class,
            AcceleratorCaseEventTypeSeeder::class,
        ]);
    }
}
