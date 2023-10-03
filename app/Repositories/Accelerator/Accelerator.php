<?php

namespace App\Repositories\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Repositories\AbstractRepository;

class Accelerator extends AbstractRepository
{
    protected function getClassName(): string
    {
        return AcceleratorModel::class;
    }
}
