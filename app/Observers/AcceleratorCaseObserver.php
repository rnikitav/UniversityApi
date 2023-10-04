<?php

namespace App\Observers;

use App\Mail\Accelerator\CaseCreate as CaseCreateMail;
use App\Mail\Accelerator\CaseUpdateStatus as CaseUpdateStatusMail;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Services\Accelerator\CaseParticipants;
use Exception;
use Illuminate\Support\Facades\Mail;

class AcceleratorCaseObserver
{
    public function saving(AcceleratorCaseModel $instance): void
    {
        if (is_null($instance->status_id)) {
            $instance->status_id = AcceleratorCaseStatus::submitted();
        }
    }

    public function created(AcceleratorCaseModel $instance): void
    {
        $ownerEmail = $instance->accelerator->user?->mainData?->email;
        if (!is_null($ownerEmail)) {
            Mail::to($ownerEmail)->send(new CaseCreateMail($instance));
        }
    }

    public function updated(AcceleratorCaseModel $instance): void
    {
        $instance->refresh();
        if (array_key_exists('status_id', $instance->getChanges())) {
            $ownerEmail = $instance->owner?->user?->mainData?->email;
            if (!is_null($ownerEmail)) {
                Mail::to($ownerEmail)->send(new CaseUpdateStatusMail($instance));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function saved(AcceleratorCaseModel $instance): void
    {
        CaseParticipants::save($instance);
    }
}
