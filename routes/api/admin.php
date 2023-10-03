<?php

use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->prefix('admin')->group(function () {
    Route::apiResource('roles', RolesController::class)->parameters(['roles' => 'id']);
    Route::apiResource('permissions', PermissionsController::class)->only(['index','show','update'])->parameters(['permissions' => 'id']);
    Route::apiResource('users', UsersController::class)->parameters(['users' => 'id']);
});

