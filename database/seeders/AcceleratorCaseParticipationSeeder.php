<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseParticipation;

class AcceleratorCaseParticipationSeeder extends AbstractSimpleTableSeeder
{
    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseParticipation::single() => 'Единолично',
            AcceleratorCaseParticipation::group() => 'Команда',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseParticipation::class;
    }
}
