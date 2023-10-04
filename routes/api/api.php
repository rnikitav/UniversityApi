<?php

use App\Http\Controllers\Accelerator\AcceleratorCaseEventController;
use App\Http\Controllers\Accelerator\AcceleratorCaseSolutionController;
use App\Http\Controllers\Accelerator\AcceleratorController;
use App\Http\Controllers\Accelerator\AcceleratorCaseController;
use App\Http\Controllers\File\FileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('accelerators', AcceleratorController::class)
        ->except('destroy')->parameters(['accelerators' => 'id']);

    Route::apiResource('accelerators/{id}/cases', AcceleratorCaseController::class)
        ->except('destroy')->parameters(['cases' => 'case_id']);
    Route::patch('accelerators/{id}/cases/{case_id}/change-status', [AcceleratorCaseController::class, 'updateStatus']);
    Route::patch('accelerators/{id}/cases/{case_id}/set-score', [AcceleratorCaseController::class, 'setScore']);
    Route::patch('accelerators/{id}/cases/{case_id}/publish', [AcceleratorCaseController::class, 'publish']);

    Route::apiResource('accelerators/{id}/cases/{case_id}/events', AcceleratorCaseEventController::class)
        ->except('destroy')->parameters(['events' => 'event_id']);

    Route::apiResource('accelerators/{id}/cases/{case_id}/solutions', AcceleratorCaseSolutionController::class)
        ->except('destroy')->parameters(['solutions' => 'solution_id']);
    Route::patch('accelerators/{id}/cases/{case_id}/solutions/{solution_id}/change-status', [AcceleratorCaseSolutionController::class, 'updateStatus']);
    Route::patch('accelerators/{id}/cases/{case_id}/solutions/{solution_id}/send-message', [AcceleratorCaseSolutionController::class, 'sendMessage']);

    Route::get('file/{id}/{hash}', [FileController::class, 'download']);
    Route::delete('file/{id}/{hash}', [FileController::class, 'destroy']);
});
