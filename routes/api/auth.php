<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::name('auth.')->prefix('auth')->middleware('throttle')->group(function () {
    
    Route::post('login', [AuthController::class, 'login'])
        ->name('login');
    
    Route::get('refresh', [AuthController::class, 'refresh'])
        ->name('refresh');
    
    Route::middleware('auth:api')
        ->post('logout', [AuthController::class, 'logout'])
        ->name('logout');
    
    Route::post('forgot', [AuthController::class, 'forgot'])
        ->name('forgot');
    
    Route::post('change-password', [AuthController::class, 'changePassword'])
        ->name('change-password');
    
    Route::post('token', [AccessTokenController::class, "issueToken"])
        ->name('token');
    
});

