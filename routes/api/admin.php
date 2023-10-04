<?php

use App\Http\Controllers\Admin\ImageCollectionController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\NewsController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->prefix('admin')->group(function () {
    Route::apiResource('roles', RolesController::class)->parameters(['roles' => 'id']);
    Route::apiResource('permissions', PermissionsController::class)->only(['index','show','update'])->parameters(['permissions' => 'id']);
    Route::apiResource('users', UsersController::class)->parameters(['users' => 'id']);
    Route::apiResource('news', NewsController::class)->parameters(['news' => 'id']);
    Route::apiResource('image-collections', ImageCollectionController::class)->parameters(['image-collections' => 'id']);
});

