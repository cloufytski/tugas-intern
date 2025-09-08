<?php

use Illuminate\Support\Facades\Route;
use Modules\InvBalance\Http\Controllers\DashboardBalanceController;
use Modules\InvBalance\Http\Controllers\InventoryBalanceController;
use Modules\InvBalance\Http\Controllers\InventoryCheckpointController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware('auth:sanctum')->prefix('inventory')->name('inventory.')->group(function () {
    Route::post('production', [InventoryBalanceController::class, 'inventoryProduction'])->name('production');
    Route::post('sales', [InventoryBalanceController::class, 'inventorySales'])->name('sales');
    Route::post('procurement', [InventoryBalanceController::class, 'inventoryProcurement'])->name('procurement');
    Route::post('balance', [InventoryBalanceController::class, 'inventoryBalance'])->name('balance');
    Route::post('refresh', [InventoryBalanceController::class, 'refreshInventoryView'])->name('refresh');
    Route::get('log', [InventoryBalanceController::class, 'logTimestamps'])->name('log');
    Route::post('total', [DashboardBalanceController::class, 'inventoryTotal'])->name('total');

    Route::apiResource('checkpoint', InventoryCheckpointController::class)->names('checkpoint');
});
