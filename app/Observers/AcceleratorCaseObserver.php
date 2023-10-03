<?php

namespace App\Observers;

use App\Events\FileDeleting;
use App\Mail\Accelerator\CaseCreate as CaseCreateMail;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Services\Accelerator\CaseParticipants;
use App\Services\SaveFiles;
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
        $ownerEmail = $instance->accelerator->user->mainData->email;
        if (!is_null($ownerEmail)) {
            Mail::to($ownerEmail)->send(new CaseCreateMail($instance));
        }
    }

    /**
     * @throws Exception
     */
    public function saved(AcceleratorCaseModel $instance): void
    {
        CaseParticipants::save($instance);
        SaveFiles::save($instance);
    }

    public function deleting(AcceleratorCaseModel $instance): void
    {
        foreach ($instance->files as $file) {
            FileDeleting::dispatch($file);
            $file->delete();
        }
    }
}
