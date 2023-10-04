<?php

namespace Tests\Generators\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use Database\Factories\Accelerator\AcceleratorControlPointFactory;
use Illuminate\Database\Eloquent\Collection;

class AcceleratorControlPoint
{
    protected static function getBaseFactory(int $count = null, array $data = []): AcceleratorControlPointFactory
    {
        /** @var AcceleratorControlPointFactory $factory */
        $factory = AcceleratorControlPointModel::factory($count);
        if ($data) {
            $factory = $factory->state($data);
        } else {
            $factory = $factory->mock();
        }
        return $factory;
    }

    public static function create(AcceleratorModel $accelerator, int $count = null, array $data = []): AcceleratorControlPointModel | Collection
    {
        return static::getBaseFactory($count, $data)
            ->accelerator($accelerator->id)
            ->create();
    }
}
