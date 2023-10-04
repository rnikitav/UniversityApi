<?php

namespace Tests\Generators;

use App\Models\Tags\ImageCollection as ImageCollectionModel;
use Database\Factories\Tags\ImageCollectionFactory;
use Illuminate\Database\Eloquent\Collection;

class ImageCollection
{
    protected static function getBaseFactory(int $count = null, array $data = []): ImageCollectionFactory
    {
        /** @var ImageCollectionFactory $factory */
        $factory = ImageCollectionModel::factory($count);
        if (!$data) {
            $factory = $factory->mock();
        } else {
            $factory = $factory->state($data);
        }
        return $factory;
    }

    public static function create(int $count = null, array $data = []): ImageCollectionModel | Collection
    {
        return static::getBaseFactory($count, $data)->create();
    }
}
