<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accelerator\Case\Create as CreateRequest;
use App\Http\Requests\Accelerator\Case\Update as UpdateRequest;
use App\Http\Requests\Accelerator\Case\UpdateStatus as UpdateStatusRequest;
use App\Http\Resources\Accelerator\AcceleratorCase as AcceleratorCaseResource;
use App\Http\Resources\Accelerator\AcceleratorCaseShort as AcceleratorCaseShortResource;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class AcceleratorCaseController extends Controller
{
    protected AcceleratorRepository $acceleratorRepository;
    protected ?AcceleratorModel $accelerator;
    protected ?AcceleratorCaseModel $case;
    protected ?UserModel $currentUser;

    public function __construct(AcceleratorRepository $acceleratorRepository, Request $request)
    {
        if ($request->method() == 'POST') {
            $this->middleware('permission:' . Permission::getPermissionStudent());
        }

        $this->acceleratorRepository = $acceleratorRepository;
        $this->currentUser = $request->user();
    }

    public function index(int $id): Response
    {
        $this->getAccelerator($id);
        if ($this->accelerator->user()->is($this->currentUser)) {
            return response(AcceleratorCaseShortResource::collection($this->accelerator->cases));
        } else {
            return response(AcceleratorCaseShortResource::collection($this->accelerator->approvedCases));
        }
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request, int $id): Response
    {
        $data = $request->prepareData();

        $this->getAccelerator($id);

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var AcceleratorCaseModel $new */
            $new = $this->accelerator->cases()
                ->make()
                ->fill($data);
            $new->setParticipants([['user_id' => $this->currentUser->id, 'role_id' => AcceleratorCaseRole::owner()]]);
            $new->setAttachments($data['files'] ?? []);
            $new->save();

            return $new;
        });

        return response(new AcceleratorCaseResource($new->refresh()));
    }

    public function show(int $id, int $case_id): Response
    {
        $this->getAcceleratorCase($id, $case_id);
        $this->checkOwners();

        return response(new AcceleratorCaseResource($this->case));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id, int $case_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);

        if ($this->currentUser->isNot($this->case->owner?->user)) {
            throw new OperationNotPermittedException();
        }

        if (!$this->case->canEditable()) {
            throw new OperationNotPermittedException();
        }

        DBUtils::inTransaction(function () use ($data) {
            $this->case->fill($data);
            $this->case->setAttachments($data['files'] ?? []);
            $this->case->update();
        });

        return response(new AcceleratorCaseResource($this->case->refresh()));
    }

    /**
     * @throws Throwable
     */
    public function updateStatus(UpdateStatusRequest $request, int $id, int $case_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);
        $this->checkOwners();

        if ($this->currentUser->is($this->case->owner?->user)) {
            if (!$this->case->canEditable() || $data['status_id'] != AcceleratorCaseStatus::submitted()) {
                throw new OperationNotPermittedException();
            }
        } else {
            if ($data['status_id'] == AcceleratorCaseStatus::submitted()) {
                throw new OperationNotPermittedException();
            }
        }

        DBUtils::inTransaction(function () use ($data) {
            $this->case->fill($data);
            $this->case->setMessages($data['messages']);
            $this->case->update();
        });

        return response(new AcceleratorCaseResource($this->case));
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
}
