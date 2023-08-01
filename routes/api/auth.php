<?php

use Illuminate\Support\Facades\Route;

Route::name('auth.')->prefix('auth')->middleware('throttle')->group(function () {
    Route::post('registration', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@registration',
        'as' => 'registration',
    ]);
    Route::post('confirm', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@confirm',
        'as' => 'confirm',
    ]);
    Route::post('retry-activate', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@retryActivate',
        'as' => 'retry-activate',
    ]);
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
        'middleware' => 'verified',
    ]);
    Route::post('check-email', [
        'uses' => '\App\Http\Controllers\Auth\AuthController@checkEmail',
        'as' => 'check-email',
    ]);
});

