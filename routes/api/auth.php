<?php

use Illuminate\Support\Facades\Route;

Route::name('auth.')->prefix('auth')->middleware('throttle')->group(function () {
    Route::post('forgot', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@forgot',
        'as' => 'forgot',
    ]);
    Route::post('change-password', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@changePassword',
        'as' => 'change-password',
    ]);
    Route::post('token', [
        'uses' => '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
        'as' => 'token',
    ]);
});

