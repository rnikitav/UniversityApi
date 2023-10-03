<?php

namespace App\Repositories\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase;
use App\Repositories\AbstractRepository;

class Accelerator extends AbstractRepository
{
    protected function getClassName(): string
    {
        return AcceleratorModel::class;
    }

    public function caseByIdOr404(AcceleratorModel $accelerator, int $caseId): AcceleratorCase
    {
        $case = $accelerator->cases()->where('id', $caseId)->get()->first();
        if (is_null($case)) {
            abort(404);
        }

        return $case;
    }
}
