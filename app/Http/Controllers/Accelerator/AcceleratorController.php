<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accelerator\Create as CreateRequest;
use App\Http\Requests\Accelerator\Update as UpdateRequest;
use App\Http\Resources\Accelerator\Accelerator as AcceleratorResource;
use App\Http\Resources\Accelerator\AcceleratorShortCollection;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Utils\DB as DBUtils;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class AcceleratorController extends Controller
{
    protected AcceleratorRepository $acceleratorRepository;

    public function __construct(AcceleratorRepository $acceleratorRepository, Request $request)
    {
        if (in_array($request->method(), ['PATCH','POST'])) {
            $this->middleware('permission:accelerators.edit');
        }
        $this->acceleratorRepository = $acceleratorRepository;
    }

    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->acceleratorRepository->paginate($perPage)
            : $this->acceleratorRepository->all();

        return response(new AcceleratorShortCollection($items));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request): Response
    {
        $data = $request->only(Helpers::keysRules($request));
        $data['user_id'] = $request->user()->id;

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var AcceleratorModel $new */
            $new = AcceleratorModel::factory()
                ->make()
                ->fill($data);
            $new->setControlPoints($data['control_points'] ?? []);
            $new->setAttachments($data['files'] ?? []);
            $new->save();

            return $new;
        });

        return response(new AcceleratorResource($new->refresh()));
    }

    public function show(int $id): Response
    {
        $item = $this->acceleratorRepository->byIdOr404($id);
        return response(new AcceleratorResource($item));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id): Response
    {
        /** @var AcceleratorModel $item */
        $item = $this->acceleratorRepository->byIdOr404($id);

        if ($request->user()->isNot($item->user)) {
            throw new OperationNotPermittedException();
        }

        $data = $request->only(Helpers::keysRules($request));

        DBUtils::inTransaction(function () use ($data, $item) {
            $item->setControlPoints($data['control_points'] ?? []);
            $item->update($data);
        });

        return response(new AcceleratorResource($item->refresh()));
    }
}
