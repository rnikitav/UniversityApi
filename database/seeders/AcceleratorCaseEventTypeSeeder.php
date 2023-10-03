<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseEventType;

class AcceleratorCaseEventTypeSeeder extends AbstractSimpleTableSeeder
{
    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseEventType::enter() => 'Участие в команде',
            AcceleratorCaseEventType::exit() => 'Выход из команды',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseEventType::class;
    }
}
