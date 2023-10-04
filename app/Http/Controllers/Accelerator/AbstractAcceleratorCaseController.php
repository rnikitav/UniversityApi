<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Controllers\Controller;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use Illuminate\Http\Request;

abstract class AbstractAcceleratorCaseController extends Controller
{
    protected AcceleratorRepository $acceleratorRepository;
    protected ?AcceleratorModel $accelerator;
    protected ?AcceleratorCaseModel $case;
    protected ?UserModel $currentUser;

    public function __construct(AcceleratorRepository $acceleratorRepository, Request $request)
    {
        $this->acceleratorRepository = $acceleratorRepository;
        $this->currentUser = $request->user();
    }

    protected function getAccelerator(int $accelerator_id): void
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->accelerator = $this->acceleratorRepository->byIdOr404($accelerator_id);
    }

    protected function getAcceleratorCase(int $accelerator_id, int $case_id): void
    {
        $this->getAccelerator($accelerator_id);
        $this->case = $this->acceleratorRepository->caseByIdOr404($this->accelerator, $case_id);
    }

    protected function checkOwners(): void
    {
        if ($this->currentUser->isNot($this->case->owner?->user)
            && $this->currentUser->isNot($this->accelerator->user)
        ) {
            throw new OperationNotPermittedException();
        }
    }

    protected function checkIsExpert(): void
    {
        $isExpert = $this->currentUser->hasPermissionTo(Permission::getPermissionExpert());
        if (!$isExpert) {
            throw new OperationNotPermittedException();
        }
    }
}
