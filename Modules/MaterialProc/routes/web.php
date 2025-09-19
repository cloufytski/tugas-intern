<?php

use Illuminate\Support\Facades\Route;
use Modules\MaterialProc\Http\Controllers\SupplierController;
use Modules\MaterialProc\Http\Controllers\MaterialProcController;
use Modules\MaterialProc\Http\Controllers\ProcurementController;
use Modules\MaterialProc\Http\Controllers\MbProductController;

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

Route::group([], function () {
    Route::resource('materialproc', MaterialProcController::class)->names('materialproc');
});

Route::middleware('auth:sanctum')->prefix('procurement')->name('procurement.')->group(function () {
    // route custom
    Route::post('procurement/{id}/simple', [ProcurementController::class, 'updateQtyOrEta'])->name('procurement.update.simple'); // only for update Qty or Eta in popover
    Route::post('dashboard/material-total', [MaterialProcController::class, 'materialTotal'])->name('dashboard.material.total');

    Route::resource('supplier', SupplierController::class)->names('supplier');
    Route::resource('procurement', ProcurementController::class)->names('procurement');
    Route::resource('mb-product', MbProductController::class)->names('mb-product');

    // route view
    Route::get('receipt', [ProcurementController::class, 'view'])->name('procurement.view');
    Route::get('dashboard', [MaterialProcController::class, 'dashboardView'])->name('dashboard.view');
    Route::get('mb-products', [MbProductController::class, 'view'])->name('mb-product.view');
});

// master data
Route::middleware('auth')->prefix('master')->name('master.')->group(function () {
    Route::get('supplier', [SupplierController::class, 'view'])->name('supplier.view');
});
