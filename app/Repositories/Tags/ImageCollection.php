<?php

namespace App\Repositories\Tags;

use App\Models\Tags\ImageCollection as ImageCollectionModel;
use App\Repositories\AbstractRepository;

class ImageCollection extends AbstractRepository
{
    protected function getClassName(): string
    {
        return ImageCollectionModel::class;
    }
}
