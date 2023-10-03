<?php

use App\Http\Controllers\Accelerator\AcceleratorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('accelerators', AcceleratorController::class)->except('destroy')->parameters(['accelerators' => 'id']);
});
