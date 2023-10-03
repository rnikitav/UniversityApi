<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;

class AcceleratorCaseSolutionStatusSeeder extends AbstractSimpleTableSeeder
{
    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseSolutionStatus::submitted() => 'Подано',
            AcceleratorCaseSolutionStatus::approved() => 'Одобрено',
            AcceleratorCaseSolutionStatus::sentRevision() => 'На доработку',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseSolutionStatus::class;
    }
}
