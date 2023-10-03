<?php

namespace App\Observers;

use App\Events\FileDeleting;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorStatus;
use App\Services\Accelerator\ControlPoints;
use App\Services\SaveFiles;
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
        SaveFiles::save($instance);
    }

    public function deleting(AcceleratorModel $instance): void
    {
        foreach ($instance->files as $file) {
            FileDeleting::dispatch($file);
            $file->delete();
        }
    }
}
