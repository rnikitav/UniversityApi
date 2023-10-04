<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\Create as CreateRequest;
use App\Http\Requests\Tags\Update as UpdateRequest;
use App\Http\Resources\Tags\Tag as TagResource;
use App\Http\Resources\Tags\TagShortCollection;
use App\Models\Tags\Tag as TagModel;
use App\Repositories\Tags\Tag as TagRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class TagsController extends Controller
{
    protected TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->middleware('permission:tags.edit');
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->tagRepository->paginate($perPage)
            : $this->tagRepository->all();

        return response(new TagShortCollection($items));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request): Response
    {
        $data = $request->prepareData();

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var TagModel $new */
            $new = TagModel::factory()->make()->fill($data);
            $new->save();

            if (count($data['image_collections'])) {
                $new->imageCollections()->sync($data['image_collections']);
            }

            return $new;
        });

        return response(new TagResource($new));
    }

    public function show(int $id): Response
    {
        $item = $this->tagRepository->byIdOr404($id);
        return response(new TagResource($item));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id): Response
    {
        /** @var TagModel $item */
        $item = $this->tagRepository->byIdOr404($id);
        $data = $request->prepareData();

        DBUtils::inTransaction(function () use ($data, $item) {
            $item->update($data);
            $item->imageCollections()->sync($data['image_collections']);
        });

        return response(new TagResource($item->refresh()));
    }

    public function destroy(int $id): Response
    {
        $item = $this->tagRepository->byIdOr404($id);
        $item->delete();

        return response(['status' => true]);
    }
}
