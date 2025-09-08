<?php

use App\Http\Controllers\Authentications\AuthController;
use App\Http\Controllers\Authentications\UserController;
use App\Http\Controllers\Log\LogModuleController;
use App\Http\Controllers\Log\LogTransactionController;
use App\Http\Controllers\Master\Material\MaterialCategoryController;
use App\Http\Controllers\Master\Material\MaterialClassController;
use App\Http\Controllers\Master\Material\MaterialGroupController;
use App\Http\Controllers\Master\Material\MaterialGroupSimpleController;
use App\Http\Controllers\Master\Material\MaterialPackagingClassController;
use App\Http\Controllers\Master\Material\MaterialPackagingController;
use App\Http\Controllers\Master\Material\MaterialController;
use App\Http\Controllers\Master\Material\MaterialUomController;
use App\Http\Controllers\Master\PlantController;
use App\Http\Controllers\Master\SectionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserPreferencesController;
use Illuminate\Support\Facades\Route;

//Laravel Sanctum for API Bearer token
Route::get('my-user', [AuthController::class, 'user'])->name('api.my-user')->middleware('auth:sanctum');
Route::post('login', [AuthController::class, 'login'])->name('api.login');
Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('logout', [AuthController::class, 'logout'])->name('api.logout')->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->prefix('master')->name('api.master.')->group(function () {
    Route::apiResource('plant', PlantController::class)->names('plant');
    Route::apiResource('section', SectionController::class)->names('section');
    Route::apiResource('material/packaging/class', MaterialPackagingClassController::class)->names('material.packaging.class');
    Route::apiResource('material/packaging', MaterialPackagingController::class)->names('material.packaging');
    Route::apiResource('material/uom', MaterialUomController::class)->names('material.uom');
    Route::apiResource('material/group/simple', MaterialGroupSimpleController::class)->names('material.group.simple');
    Route::apiResource('material/group', MaterialGroupController::class)->names('material.group');
    Route::apiResource('material/category', MaterialCategoryController::class)->names('material.category');
    Route::apiResource('material/class', MaterialClassController::class)->names('material.class');
    Route::apiResource('material', MaterialController::class)->names('material');
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::apiResource('user', UserController::class)->names('user');
    Route::apiResource('role', RoleController::class)->names('role')->only(['index']);
    Route::apiResource('user-preferences', UserPreferencesController::class)->names('user-preferences')->only(['index', 'store']);

    Route::apiResource('log/module', LogModuleController::class)->names('log.module');
    Route::apiResource('log/transaction', LogTransactionController::class)->names('log.transaction');
});
