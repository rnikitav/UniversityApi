<?php

namespace App\Repositories\Tags;

use App\Models\Tags\Tag as TagModel;
use App\Repositories\AbstractRepository;

class Tag extends AbstractRepository
{
    protected function getClassName(): string
    {
        return TagModel::class;
    }
}
