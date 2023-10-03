<?php

use App\Http\Controllers\Accelerator\AcceleratorController;
use App\Http\Controllers\Accelerator\AcceleratorCaseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('accelerators', AcceleratorController::class)
        ->except('destroy')->parameters(['accelerators' => 'id']);
    Route::apiResource('accelerators/{id}/cases', AcceleratorCaseController::class)
        ->except('destroy')->parameters(['cases' => 'case_id'])->where(['case_id' => '^[1-9][0-9]*']);
});
