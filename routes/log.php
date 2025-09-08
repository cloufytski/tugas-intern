<?php

use App\Http\Controllers\Log\LogModuleController;
use App\Http\Controllers\Log\LogTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::apiResource('log/module', LogModuleController::class)->names('log.module');
    Route::apiResource('log/transaction', LogTransactionController::class)->names('log.transaction');

    // Route View
    Route::get('logs', [LogTransactionController::class, 'view'])->name('log.transaction.view');
});
