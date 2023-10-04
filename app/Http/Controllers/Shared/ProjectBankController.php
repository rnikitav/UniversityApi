<?php

namespace App\Http\Controllers\Shared;

use App\DTO\Accelerator as AcceleratorDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\Accelerator\AcceleratorWithCompleteCases;
use App\Models\Accelerator\Accelerator;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ProjectBankController extends Controller
{
    protected AcceleratorRepository $acceleratorRepository;

    public function __construct(AcceleratorRepository $acceleratorRepository)
    {
        $this->acceleratorRepository = $acceleratorRepository;
    }

    public function index(): Response
    {
        $accelerators = $this->acceleratorRepository->withPublishedCases();

        $resultCollection = new Collection();
        $accelerators->each(function (Accelerator $accelerator) use ($resultCollection) {
            $lastPoint = $accelerator->lastPoint();
            if (is_null($lastPoint)) {
                return;
            }

            $completedCases = $lastPoint->getCompletedCases();

            $resultCollection->push(AcceleratorDTO::from(['model' => $accelerator, 'cases' => $completedCases]));
        });

        return response(AcceleratorWithCompleteCases::collection($resultCollection));
    }
}
