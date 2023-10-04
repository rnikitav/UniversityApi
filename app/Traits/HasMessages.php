<?php

namespace App\Traits;

use App\Models\Accelerator\Case\AcceleratorCaseMessage;
use App\Services\Accelerator\CaseMessages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMessages
{
    protected array $savingMessages = [];

    public static function bootHasMessages(): void
    {
        static::saved(function (Model $model) {
            CaseMessages::save($model);
        });
    }

    public function messages(): MorphMany
    {
        return $this->morphMany(AcceleratorCaseMessage::class, 'owner');
    }

    public function setMessages(array $messages): void
    {
        $this->savingMessages = $messages;
    }

    public function getMessages(): array
    {
        return $this->savingMessages;
    }
}
