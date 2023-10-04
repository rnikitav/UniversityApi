<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Favorite as FavoriteRequest;
use App\Http\Resources\User\FavoriteCollection;
use App\Models\User\User as UserModel;
use App\Repositories\User\Favorites as FavoritesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoritesController extends Controller
{
    protected FavoritesRepository $favoritesRepository;
    protected ?UserModel $currentUser;

    public function __construct(FavoritesRepository $favoritesRepository, Request $request)
    {
        $this->favoritesRepository = $favoritesRepository;
        $this->currentUser = $request->user();
    }

    public function index(Request $request): Response
    {
        $perPage = $request->query('per_page') ? intval($request->query('per_page')) : 15;

        $items = $request->query('lazy')
            ? $this->favoritesRepository->paginate($perPage)
            : $this->favoritesRepository->all();

        return response(new FavoriteCollection($items));
    }

    public function store(FavoriteRequest $request): Response
    {
        $this->currentUser->favoriteCases()->sync([$request->id], false);

        return response(['status' => true]);
    }

    public function destroy(FavoriteRequest $request): Response
    {
        $this->currentUser->favoriteCases()->detach($request->id);

        return response(['status' => true]);
    }
}
