<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Requests\Accelerator\Case\Create as CreateRequest;
use App\Http\Requests\Accelerator\Case\Update as UpdateRequest;
use App\Http\Requests\Accelerator\Case\UpdateStatus as UpdateStatusRequest;
use App\Http\Resources\Accelerator\AcceleratorCase as AcceleratorCaseResource;
use App\Http\Resources\Accelerator\AcceleratorCaseShort as AcceleratorCaseShortResource;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Models\Permissions\Permission;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class AcceleratorCaseController extends AbstractAcceleratorCaseController
{
    public function __construct(AcceleratorRepository $acceleratorRepository, Request $request)
    {
        if ($request->method() == 'POST') {
            $this->middleware('permission:' . Permission::getPermissionStudent());
        }

        parent::__construct($acceleratorRepository, $request);
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
}
