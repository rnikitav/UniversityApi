<?php

namespace Tests\Generators;

use App\Models\File as FileModel;
use App\Models\Tags\Tag as TagModel;
use App\Models\Tags\ImageCollection as ImageCollectionModel;
use Database\Factories\Tags\TagFactory;
use Database\Factories\Tags\ImageCollectionFactory;
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

    public static function createWithCollection(int $count = null, array $data = []): TagModel | Collection
    {
        /** @var ImageCollectionFactory $imageFactory */
        $imageFactory = ImageCollectionModel::factory();
        $imageFactory = $imageFactory->mock()
            ->has(FileModel::factory()->mock(), 'files');

        return static::getBaseFactory($count, $data)
            ->has($imageFactory, 'imageCollections')
            ->create();
    }
}
