<?php

namespace App\DTO;


use App\Models\Accelerator\Case\AcceleratorCase;
use App\Models\File;
use Spatie\LaravelData\Data;

class AcceleratorCaseCompleted extends Data
{
    public AcceleratorCase $model;
    public File $file;
}
