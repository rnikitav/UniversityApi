<?php

namespace Tests\Generators\Accelerator;

use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseSolution as AcceleratorCaseSolutionModel;
use Database\Factories\Accelerator\Case\AcceleratorCaseSolutionFactory;
use Illuminate\Database\Eloquent\Collection;

class AcceleratorCaseSolution
{
    protected static function getBaseFactory(AcceleratorCaseModel $case, int $count = null, array $data = []): AcceleratorCaseSolutionFactory
    {
        /** @var AcceleratorCaseSolutionFactory $factory */
        $factory = AcceleratorCaseSolutionModel::factory($count);
        if ($data) {
            $factory = $factory->state($data);
        } else {
            $factory = $factory->mock($case->owner->user?->id);
        }
        return $factory;
    }

    public static function create(AcceleratorCaseModel $case, AcceleratorControlPointModel $point, int $count = null): AcceleratorCaseSolutionModel | Collection
    {
        return static::getBaseFactory($case, $count)
            ->case($case->id)
            ->point($point->id)
            ->create();
    }
}
