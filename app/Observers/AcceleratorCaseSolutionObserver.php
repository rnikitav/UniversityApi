<?php

namespace App\Observers;

use App\Models\Accelerator\Case\AcceleratorCaseSolution as AcceleratorCaseSolutionModel;
use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;

class AcceleratorCaseSolutionObserver
{
    public function saving(AcceleratorCaseSolutionModel $instance): void
    {
        if (is_null($instance->status_id)) {
            $instance->status_id = AcceleratorCaseSolutionStatus::submitted();
        }
    }
}
