<?php

use App\Http\Controllers\User\CaseController;
use App\Http\Controllers\User\FavoritesController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::name('user.')->prefix('user')->group(function () {
        Route::get('me', [UserController::class, 'me'])
            ->name('me');

        Route::get('favorites', [FavoritesController::class, 'index'])->name('favorites.index');
        Route::post('favorites', [FavoritesController::class, 'store'])->name('favorites.store');
        Route::delete('favorites', [FavoritesController::class, 'destroy'])->name('favorites.destroy');

        Route::get('cases/completed', [CaseController::class, 'completed'])
            ->name('cases.completed');
    });
});

