<?php

namespace Tests\Generators;

use App\Models\Tags\Tag as TagModel;
use Database\Factories\Tags\TagFactory;
use Illuminate\Database\Eloquent\Collection;

class Tag
{
    protected static function getBaseFactory(int $count = null, array $data = []): TagFactory
    {
        /** @var TagFactory $factory */
        $factory = TagModel::factory($count);
        if (!$data) {
            $factory = $factory->mock();
        } else {
            $factory = $factory->state($data);
        }
        return $factory;
    }

    public static function create(int $count = null, array $data = []): TagModel | Collection
    {
        return static::getBaseFactory($count, $data)->create();
    }
}
