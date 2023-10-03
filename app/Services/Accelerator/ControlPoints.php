<?php

namespace App\Services\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint;
use Illuminate\Support\Arr;

class ControlPoints
{
    public static function save(AcceleratorModel $accelerator): void
    {
        if ($accelerator->controlPoints->count() == 0) {
            static::create($accelerator);
        } else {
            static::update($accelerator);
        }
    }

    protected static function create(AcceleratorModel $accelerator): void
    {
        foreach ($accelerator->getControlPoints() as $controlPoint) {
            $accelerator->controlPoints()->create($controlPoint);
        }
    }

    protected static function update(AcceleratorModel $accelerator): void
    {
        foreach ($accelerator->getControlPoints() as $controlPoint) {
            if (!array_key_exists('id', $controlPoint)) {
                continue;
            }

            /** @var AcceleratorControlPoint $find */
            $find = $accelerator->controlPoints->first(fn ($item) => $item->id == $controlPoint['id']);
            if (!$find) {
                continue;
            }

            $find->update(Arr::only($controlPoint, 'date_completion'));
        }
    }
}
