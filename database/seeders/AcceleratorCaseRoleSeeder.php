<?php

namespace Database\Seeders;

use App\Models\Accelerator\Case\AcceleratorCaseRole;

class AcceleratorCaseRoleSeeder extends AbstractSimpleTableSeeder
{
    protected array $statuses;

    public function __construct()
    {
        parent::__construct();
        $this->statuses = [
            AcceleratorCaseRole::owner() => 'Владелец',
            AcceleratorCaseRole::participant() => 'Участник',
        ];
    }

    protected function getClassName(): string
    {
        return AcceleratorCaseRole::class;
    }
}
