<?php

namespace App\Observers;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorStatus;
use App\Services\Accelerator\ControlPoints;
use Exception;

class AcceleratorObserver
{
    public function saving(AcceleratorModel $instance): void
    {
        if (is_null($instance->status_id)) {
            $instance->status_id = AcceleratorStatus::notPublished();
        }
        if (!is_null($instance->published_at) && $instance->status_id == AcceleratorStatus::notPublished()) {
            $instance->status_id = AcceleratorStatus::acceptApplications();
        }
    }

    /**
     * @throws Exception
     */
    public function saved(AcceleratorModel $instance): void
    {
        ControlPoints::save($instance);
    }

}
