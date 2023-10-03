<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\Inner\InvalidDatabaseSetException;
use App\Exceptions\Inner\InvalidDataSetException;
use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accelerator\Create as CreateRequest;
use App\Http\Resources\Accelerator\AcceleratorShortCollection;
use App\Http\Resources\Accelerator\Accelerator as AcceleratorResource;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Utils\Helpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        DB::beginTransaction();

        try {
            $new = AcceleratorModel::factory()->create($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            $channel = $exception instanceof InvalidDatabaseSetException ? 'database' : config('logging.default');
            $data = $exception instanceof InvalidDataSetException ? $exception->getData() : [];
            Log::channel($channel)->error($exception->getMessage(), $data);

            throw new ServerErrorException();
        }

        return response(new AcceleratorResource($new->refresh()));
    }

    public function show(int $id): Response
    {
        $item = $this->acceleratorRepository->byIdOr404($id);
        return response(new AcceleratorResource($item));
    }

    /*
    public function update(PermissionEditRequest $request, int $id): Response
    {
        $item = $this->acceleratorRepository->byIdOr404($id);
        $item->update($request->only(Helpers::keysRules($request)));

        return response(new AcceleratorResource($item));
    }
    */
}
