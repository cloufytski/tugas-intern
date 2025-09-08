<?php

use App\Http\Controllers\Authentications\AuthController;
use App\Http\Controllers\Authentications\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserPreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [AuthController::class, 'registerView'])->name('register.view');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    // Login from web with middleware 'guest' different from api.php AuthController
    Route::get('login', [AuthController::class, 'loginView'])->name('login.view');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('my-user', [AuthController::class, 'user'])->name('my-user');

    // Route view
    Route::get('admin/user', [UserController::class, 'view'])->name('user.view'); // need to rename URL since it can clashed with Route::resource
    Route::get('user/register', [UserController::class, 'registerView'])->name('user.view.register');
    Route::get('user/{id}/reset-password', [UserController::class, 'resetPasswordView'])->name('user.view.reset-password');
    Route::post('user/{id}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');

    // User management
    Route::resource('user', UserController::class)->names('user'); // because use UserDataTable, not calling ajax API
    Route::resource('role', RoleController::class)->names('role')->only(['index']);
    Route::resource('user-preferences', UserPreferencesController::class)->names('user-preferences')->only(['index', 'store', 'destroy']);
});
