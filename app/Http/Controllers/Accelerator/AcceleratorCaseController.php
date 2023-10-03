<?php

namespace App\Http\Controllers\Accelerator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accelerator\Case\Create as CreateRequest;
use App\Http\Resources\Accelerator\AcceleratorCase as AcceleratorCaseResource;
use App\Http\Resources\Accelerator\AcceleratorCaseShort as AcceleratorCaseShortResource;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use App\Models\Permissions\Permission;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class AcceleratorCaseController extends Controller
{
    protected AcceleratorRepository $acceleratorRepository;

    public function __construct(AcceleratorRepository $acceleratorRepository, Request $request)
    {
        if ($request->method() == 'POST') {
            $this->middleware('permission:' . Permission::getPermissionStudent());
        }

        $this->acceleratorRepository = $acceleratorRepository;
    }

    public function index(int $id): Response
    {
        /** @var AcceleratorModel $accelerator */
        $accelerator = $this->acceleratorRepository->byIdOr404($id);
        return response(AcceleratorCaseShortResource::collection($accelerator->cases));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request, int $id): Response
    {
        $data = $request->prepareData();

        /** @var AcceleratorModel $accelerator */
        $accelerator = $this->acceleratorRepository->byIdOr404($id);

        $new = DBUtils::inTransaction(function () use ($data, $request, $accelerator) {
            /** @var AcceleratorCaseModel $new */
            $new = $accelerator->cases()
                ->make()
                ->fill($data);
            $new->setParticipants([['user_id' => $request->user()->id, 'role_id' => AcceleratorCaseRole::owner()]]);
            $new->setAttachments($data['files'] ?? []);
            $new->save();

            return $new;
        });

        return response(new AcceleratorCaseResource($new->refresh()));
    }

    public function show(int $id, int $case_id): Response
    {
        /** @var AcceleratorModel $accelerator */
        $accelerator = $this->acceleratorRepository->byIdOr404($id);

        $case = $this->acceleratorRepository->caseByIdOr404($accelerator, $case_id);

        return response(new AcceleratorCaseResource($case));
    }

}
