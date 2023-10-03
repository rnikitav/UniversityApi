<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\Store;
use App\Http\Requests\News\Update;
use App\Http\Resources\News\NewsCollectionResource;
use App\Http\Resources\News\NewsResource;
use App\Models\News\News;
use App\Models\News\News as NewsModel;
use App\Repositories\News\NewsRepository;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class NewsController extends Controller
{
    private NewsRepository $newsRepository;


    public function __construct(NewsRepository $repository)
    {
        $this->middleware('permission:news.edit');
        $this->newsRepository = $repository;
    }


    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->newsRepository->paginate($perPage)
            : $this->newsRepository->all();

        return response(new NewsCollectionResource($items));
    }

    /**
     * @throws Throwable
     */
    public function store(Store $request): Response
    {
        $data = $request->prepareData();

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var NewsModel $new */
            $new = NewsModel::factory()
                ->make()
                ->fill($data);
            $new->setAttachments($data['files']);
            $new->save();
            return $new;
        });

        return response(new NewsResource($new->refresh()));
    }

    public function show(int $id): Response
    {
        $news = $this->newsRepository->byIdOr404($id);
        return response(new NewsResource($news));
    }

    /**
     * @throws Throwable
     */
    public function update(Update $request, int $id): Response
    {
        /** @var NewsModel $news */
        $news = $this->newsRepository->byIdOr404($id);
        $data = $request->prepareData();
        $new = DBUtils::inTransaction(function () use ($data, $news) {
            $news->setAttachments($data['files']);
            $news->update($data);
            return $news;
        });

        return response(new NewsResource($new->refresh()));
    }

    public function destroy(int $id): Response
    {
        $item = $this->newsRepository->byIdOr404($id);
        $item->delete();
        return response(['status' => true]);
    }
}
