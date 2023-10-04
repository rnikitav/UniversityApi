<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\ImageCollectionCreate as CreateRequest;
use App\Http\Requests\Tags\ImageCollectionUpdate as UpdateRequest;
use App\Http\Resources\Tags\ImageCollection as ImageCollectionResource;
use App\Http\Resources\Tags\ImageCollectionShortCollection;
use App\Models\Tags\ImageCollection as ImageCollectionModel;
use App\Repositories\Tags\ImageCollection as ImageCollectionRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class ImageCollectionController extends Controller
{
    protected ImageCollectionRepository $imageCollectionRepository;

    public function __construct(ImageCollectionRepository $imageCollectionRepository)
    {
        $this->middleware('permission:image-collection.edit');
        $this->imageCollectionRepository = $imageCollectionRepository;
    }

    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->imageCollectionRepository->paginate($perPage)
            : $this->imageCollectionRepository->all();

        return response(new ImageCollectionShortCollection($items));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request): Response
    {
        $data = $request->prepareData();

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var ImageCollectionModel $new */
            $new = ImageCollectionModel::factory()
                ->make()
                ->fill($data);
            $new->setAttachments($data['files'] ?? []);
            $new->save();

            return $new;
        });

        return response(new ImageCollectionResource($new->refresh()));
    }

    public function show(int $id): Response
    {
        $item = $this->imageCollectionRepository->byIdOr404($id);
        return response(new ImageCollectionResource($item));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id): Response
    {
        /** @var ImageCollectionModel $item */
        $item = $this->imageCollectionRepository->byIdOr404($id);
        $data = $request->prepareData();

        DBUtils::inTransaction(function () use ($data, $item) {
            $item->setAttachments($data['files'] ?? []);
            $item->update($data);
        });

        return response(new ImageCollectionResource($item->refresh()));
    }

    public function destroy(int $id): Response
    {
        $item = $this->imageCollectionRepository->byIdOr404($id);
        $item->delete();

        return response(['status' => true]);
    }
}
