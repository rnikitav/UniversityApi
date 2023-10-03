<?php

namespace Tests\Generators;

use App\Models\News\News as NewsModel;
use Database\Factories\News\NewsFactory;
use Illuminate\Database\Eloquent\Collection;

class News
{
    protected static function getBaseFactory(int $count = null, array $data = []): NewsFactory
    {
        /** @var NewsFactory $factory */
        $factory = NewsModel::factory($count);
        if (!$data) {
            $factory = $factory->mock();
        } else {
            $factory = $factory->state($data);
        }
        return $factory;
    }

    public static function create(int $count = null, array $data = []): NewsModel | Collection
    {
        return static::getBaseFactory($count, $data)->create();
    }
}
