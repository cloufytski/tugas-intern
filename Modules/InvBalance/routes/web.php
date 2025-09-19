<?php

use Illuminate\Support\Facades\Route;
use Modules\InvBalance\Http\Controllers\DashboardBalanceController;
use Modules\InvBalance\Http\Controllers\InventoryBalanceController;
use Modules\InvBalance\Http\Controllers\InventoryRawController;
use Modules\InvBalance\Http\Controllers\InventoryCheckpointController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->prefix('inventory')->name('inventory.')->group(function () {
    Route::post('production', [InventoryBalanceController::class, 'inventoryProduction'])->name('production');
    Route::post('sales', [InventoryBalanceController::class, 'inventorySales'])->name('sales');
    Route::post('procurement', [InventoryBalanceController::class, 'inventoryProcurement'])->name('procurement');
    Route::post('balance', [InventoryBalanceController::class, 'inventoryBalance'])->name('balance');
    Route::post('raw-material', [InventoryRawController::class, 'inventoryRawMaterial'])->name('raw-material');
    Route::post('refresh', [InventoryBalanceController::class, 'refreshInventoryView'])->name('refresh');
    Route::get('log', [InventoryBalanceController::class, 'logTimestamps'])->name('log');
    Route::post('total', [DashboardBalanceController::class, 'inventoryTotal'])->name('total');

    Route::resource('checkpoint', InventoryCheckpointController::class)->names('checkpoint');

    // Route view
    Route::get('balance', [InventoryBalanceController::class, 'view'])->name('balance.view');
    Route::get('raw-material', [InventoryRawController::class, 'view'])->name('raw-material.view');
    Route::get('checkpoints', [InventoryCheckpointController::class, 'view'])->name('checkpoint.view');
    Route::get('dashboard', [DashboardBalanceController::class, 'dashboardView'])->name('dashboard.view');
});
