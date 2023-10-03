<?php

namespace App\Repositories\News;

use App\Models\News\News as NewsModel;
use App\Repositories\AbstractRepository;

class NewsRepository extends AbstractRepository
{
    protected function getClassName(): string
    {
        return NewsModel::class;
    }
}
