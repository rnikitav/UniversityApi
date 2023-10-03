<?php

namespace Database\Seeders;

use Database\Seeders\Develop\NewsSeeder;
use Illuminate\Database\Seeder;

class DevelopSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            NewsSeeder::class
        ]);
    }
}
