<?php

namespace Database\Seeders\Develop;

use App\Models\News\News;
use Database\Factories\News\NewsFactory;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{

    public function run(): void
    {
        News::factory(2)->mock();
    }
}
