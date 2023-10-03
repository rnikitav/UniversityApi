<?php

namespace Database\Seeders\Develop;

use App\Models\News\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{

    public function run(): void
    {
        News::factory(2)->mock()->create();
    }
}
