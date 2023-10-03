<?php

namespace App\Services\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;

class CaseMessages
{
    public static function save(AcceleratorCaseModel $instance): void
    {
        foreach ($instance->getMessages() as $message) {
            $instance->messages()->create($message);
        }
    }
}
