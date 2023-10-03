<?php

namespace App\Services\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;

class CaseParticipants
{
    public static function save(AcceleratorCaseModel $instance): void
    {
        foreach ($instance->getParticipants() as $participant) {
            $instance->participants()->create($participant);
        }
    }
}
