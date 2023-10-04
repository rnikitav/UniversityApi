<?php

namespace App\Services\Accelerator;

use Illuminate\Database\Eloquent\Model;

class CaseMessages
{
    public static function save(Model $model): void
    {
        $messages = method_exists($model, 'getMessages') ? $model->getMessages() : [];
        foreach ($messages as $message) {
            $model->messages()->create($message);
        }
    }
}
