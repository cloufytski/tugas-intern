<?php

use Illuminate\Support\Facades\Route;
use Modules\MaterialProc\Http\Controllers\MaterialProcController;
use Modules\MaterialProc\Http\Controllers\ProcurementController;
use Modules\MaterialProc\Http\Controllers\SupplierController;
use Modules\MaterialProc\Http\Controllers\MbProductController;

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

Route::middleware(['auth:sanctum'])->prefix('procurement')->group(function () {
    // route custom
    Route::post('procurement/{id}/simple', [ProcurementController::class, 'updateQtyOrEta'])->name('procurement.update.simple');
    Route::post('dashboard/material-total', [MaterialProcController::class, 'materialTotal'])->name('dashboard.material.total');

    // Route::apiResource('materialproc', MaterialProcController::class)->names('materialproc');
    Route::apiResource('supplier', SupplierController::class)->names('supplier');
    Route::apiResource('procurement', ProcurementController::class)->names('procurement'); // raw materials procurement
    Route::apiResource('mb-product', MbProductController::class)->names('mb-product');
});
