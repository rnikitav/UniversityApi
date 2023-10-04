<?php

namespace App\DTO;


use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use App\Models\Accelerator\Accelerator as AcceleratorModel;

class Accelerator extends Data
{
    public AcceleratorModel $model;
    public Collection $cases;
}
