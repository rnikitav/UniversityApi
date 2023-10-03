<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\Store;
use App\Http\Requests\News\Update;
use App\Http\Resources\News\NewsCollectionResource;
use App\Http\Resources\News\NewsResource;
use App\Models\News\News;
use App\Models\News\News as NewsModel;
use App\Repositories\News\NewsRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @throws \Throwable
     */
    public function store(Store $request): Response
    {
        DB::beginTransaction();

        try {
            $news = NewsModel::factory()->create($request->validated());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::channel('database')->error($exception->getMessage());

            throw new ServerErrorException();
        }
        return response(new NewsResource($news->refresh()));
    }

    public function show($id): Response
    {
        $news = $this->newsRepository->byIdOr404($id);
        return response(new NewsResource($news));
    }

    /**
     * @throws \Throwable
     */
    public function update(Update $request, $id): Response
    {
        $news = $this->newsRepository->byIdOr404($id);
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $news->update($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::channel('database')->error($exception->getMessage());

            throw new ServerErrorException();
        }

        return response(new NewsResource($news->refresh()));
    }

    public function destroy(int $id): Response
    {
        $item = $this->newsRepository->byIdOr404($id);
        $item->delete();
        return response(['status' => true]);
    }
}
