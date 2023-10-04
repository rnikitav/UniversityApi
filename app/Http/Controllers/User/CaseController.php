<?php

namespace App\Http\Controllers\User;

use App\DTO\AcceleratorCaseCompleted as AcceleratorCaseCompletedDTO;
use App\DTO\Accelerator as AcceleratorDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Accelerator\AcceleratorWithCompleteCases;
use App\Models\Accelerator\Accelerator;
use App\Models\Accelerator\AcceleratorControlPoint;
use App\Models\Accelerator\Case\AcceleratorCaseSolution;
use App\Models\User\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CaseController extends Controller
{
    protected ?UserModel $currentUser;

    public function __construct(Request $request)
    {
        $this->currentUser = $request->user();
    }

    public function completed(): Response
    {
        $accelerators = $this->currentUser->accelerators()
            ->with(['controlPoints', 'controlPoints.solutions', 'controlPoints.solutions.case', 'controlPoints.solutions.case.scores'])
            ->get();

        $filtered = new Collection();
        $accelerators->each(function (Accelerator $accelerator) use ($filtered) {
            /** @var AcceleratorControlPoint $finalPoint */
            $finalPoint = $accelerator->controlPoints->sortByDesc('date_completion')->first();
            if (is_null($finalPoint) || !$finalPoint->solutions->count()) {
                return;
            }

            $casesHasScore = new Collection();
            $finalPoint->solutions->each(function (AcceleratorCaseSolution $solution) use ($casesHasScore) {
                if ($solution->case->scores->count() > 0) {
                    $file = $solution->files->first();
                    $casesHasScore->push(AcceleratorCaseCompletedDTO::from(['model' => $solution->case, 'file' => $file]));
                }
            });

            if ($casesHasScore->count() == 0) {
                return;
            }

            $filtered->push(AcceleratorDTO::from(['model' => $accelerator, 'cases' => $casesHasScore]));
        });

        return response(AcceleratorWithCompleteCases::collection($filtered));
    }
}
