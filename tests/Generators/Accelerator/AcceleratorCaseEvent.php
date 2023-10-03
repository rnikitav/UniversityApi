<?php

namespace Tests\Generators\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseEvent as AcceleratorCaseEventModel;
use Database\Factories\Accelerator\Case\AcceleratorCaseEventFactory;
use Illuminate\Database\Eloquent\Collection;

class AcceleratorCaseEvent
{
    protected static function getBaseFactory(AcceleratorCaseModel $case, int $count = null, array $data = []): AcceleratorCaseEventFactory
    {
        /** @var AcceleratorCaseEventFactory $factory */
        $factory = AcceleratorCaseEventModel::factory($count);
        if ($data) {
            $factory = $factory->state($data);
        } else {
            $factory = $factory->mock($case->owner->user?->id);
        }
        return $factory;
    }

    public static function create(AcceleratorCaseModel $case, int $count = null, array $data = []): AcceleratorCaseEventModel | Collection
    {
        return static::getBaseFactory($case, $count, $data)->case($case->id)
            ->create();
    }
}
