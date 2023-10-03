<?php

namespace App\Observers;

use App\Models\Accelerator\Case\AcceleratorCaseEvent as AcceleratorCaseEventModel;
use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Mail\Accelerator\EventCreate as EventCreateMail;
use Illuminate\Support\Facades\Mail;

class AcceleratorCaseEventObserver
{
    public function saving(AcceleratorCaseEventModel $instance): void
    {
        if (is_null($instance->status_id)) {
            $instance->status_id = AcceleratorCaseEventStatus::submitted();
        }
    }

    public function created(AcceleratorCaseEventModel $instance): void
    {
        $ownerEmail = $instance->case->accelerator->user?->mainData?->email;
        if (!is_null($ownerEmail)) {
            Mail::to($ownerEmail)->send(new EventCreateMail($instance));
        }
    }
}
