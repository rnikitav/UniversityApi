<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::name('user.')->prefix('user')->group(function () {
        Route::get('me', ['uses' => '\App\Http\Controllers\User\UserController@me', 'as' => 'me']);

        Route::get('cases/completed', ['uses' => '\App\Http\Controllers\User\CaseController@completed', 'as' => 'cases.completed']);
    });
});

