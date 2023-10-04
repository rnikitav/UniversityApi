<?php

namespace App\Repositories\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseEvent as AcceleratorCaseEventModel;
use App\Models\Accelerator\Case\AcceleratorCaseSolution as AcceleratorCaseSolutionModel;
use App\Repositories\AbstractRepository;

class Accelerator extends AbstractRepository
{
    protected function getClassName(): string
    {
        return AcceleratorModel::class;
    }

    public function caseByIdOr404(AcceleratorModel $accelerator, int $caseId): AcceleratorCaseModel
    {
        $case = $accelerator->cases()->where('id', $caseId)->get()->first();
        if (is_null($case)) {
            abort(404);
        }

        return $case;
    }

    public function eventByIdOr404(AcceleratorCaseModel $case, int $eventId): AcceleratorCaseEventModel
    {
        $event = $case->events()->where('id', $eventId)->get()->first();
        if (is_null($event)) {
            abort(404);
        }

        return $event;
    }

    public function solutionByIdOr404(AcceleratorCaseModel $case, int $solutionId): AcceleratorCaseSolutionModel
    {
        $solution = $case->solutions()->where('id', $solutionId)->get()->first();
        if (is_null($solution)) {
            abort(404);
        }

        return $solution;
    }
}
