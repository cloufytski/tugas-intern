<?php

use App\Http\Controllers\Master\Material\MaterialCategoryController;
use App\Http\Controllers\Master\Material\MaterialClassController;
use App\Http\Controllers\Master\Material\MaterialGroupController;
use App\Http\Controllers\Master\Material\MaterialGroupSimpleController;
use App\Http\Controllers\Master\Material\MaterialPackagingClassController;
use App\Http\Controllers\Master\Material\MaterialPackagingController;
use App\Http\Controllers\Master\Material\MaterialController;
use App\Http\Controllers\Master\Material\MaterialMetricController;
use App\Http\Controllers\Master\Material\MaterialUomController;
use App\Http\Controllers\Master\PlantController;
use App\Http\Controllers\Master\SectionController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\SettingsController;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth:sanctum')->prefix('master')->name('master.')->group(function () {
    Route::apiResource('plant', PlantController::class)->names('plant');
    Route::apiResource('section', SectionController::class)->names('section');
    Route::apiResource('material/packaging/class', MaterialPackagingClassController::class)->names('material.packaging.class');
    Route::apiResource('material/packaging', MaterialPackagingController::class)->names('material.packaging');
    Route::apiResource('material/uom', MaterialUomController::class)->names('material.uom');
    Route::apiResource('material/group/simple', MaterialGroupSimpleController::class)->names('material.group.simple');
    Route::apiResource('material/group', MaterialGroupController::class)->names('material.group');
    Route::apiResource('material/metric', MaterialMetricController::class)->names('material.metric');
    Route::apiResource('material/category', MaterialCategoryController::class)->names('material.category');
    Route::apiResource('material/class', MaterialClassController::class)->names('material.class');
    Route::apiResource('material', MaterialController::class)->names('material');

    // route view
    Route::get('plants', [PlantController::class, 'view'])->name('plant.view');
    Route::get('sections', [SectionController::class, 'view'])->name('section.view');
    Route::get('materials', [MaterialController::class, 'view'])->name('material.view');
});

Route::middleware('auth')->group(function () {
    // Route view misc page
    Route::get('unauthorized', [MiscController::class, 'unauthorizedPage'])->name('unauthorized');
    Route::get('coming-soon', [MiscController::class, 'comingSoonPage'])->name('coming-soon');
    Route::get('settings', [SettingsController::class, 'view'])->name('settings.view');
    Route::get('settings-account', [SettingsController::class, 'accountView'])->name('settings.account.view');
    Route::get('settings-security', [SettingsController::class, 'securityView'])->name('settings.security.view');
    Route::get('settings-filter', [SettingsController::class, 'filterView'])->name('settings.filter.view');

    // Route view dashboard
    Route::get('dashboard', [MiscController::class, 'dashboardView'])->name('dashboard');
});

// Documentation Scramble Dedoc, rename from /docs/api to /api-docs because clash with LaRecipe documentation
Scramble::registerUiRoute('api-docs')->name('api-docs.view');
Scramble::registerJsonSpecificationRoute('api-docs.json');

require __DIR__ . '/authentications.php';
require __DIR__ . '/log.php';
