<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseStatus;

class AcceleratorCaseStatusSeeder extends AbstractSimpleTableSeeder
{
    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseStatus::submitted() => 'Подана',
            AcceleratorCaseStatus::approved() => 'Одобрена',
            AcceleratorCaseStatus::sentRevision() => 'Отправлена на доработку',
            AcceleratorCaseStatus::rejected() => 'Отклонена',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseStatus::class;
    }
}
