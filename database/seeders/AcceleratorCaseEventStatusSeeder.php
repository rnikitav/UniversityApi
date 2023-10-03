<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;

class AcceleratorCaseEventStatusSeeder extends AbstractSimpleTableSeeder
{
    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseEventStatus::submitted() => 'Подана',
            AcceleratorCaseEventStatus::approved() => 'Одобрена',
            AcceleratorCaseEventStatus::rejected() => 'Отклонена',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseEventStatus::class;
    }
}
