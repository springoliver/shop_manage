<?php

use App\Http\StoreOwner\Controllers\ProfileController as StoreOwnerProfileController;
use App\Http\StoreOwner\Controllers\Auth\AuthenticatedSessionController as StoreOwnerAuthenticatedSessionController;
use App\Http\StoreOwner\Controllers\Auth\NewPasswordController as StoreOwnerNewPasswordController;
use App\Http\StoreOwner\Controllers\Auth\PasswordController as StoreOwnerPasswordController;
use App\Http\StoreOwner\Controllers\Auth\PasswordResetLinkController as StoreOwnerPasswordResetLinkController;
use Illuminate\Support\Facades\Route;

// StoreOwner Routes (Default End Users)
Route::name('storeowner.')->group(function () {
    // StoreOwner Guest Routes
    Route::middleware('guest.storeowner')->group(function () {
        Route::get('login', [StoreOwnerAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [StoreOwnerAuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [StoreOwnerPasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [StoreOwnerPasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [StoreOwnerNewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [StoreOwnerNewPasswordController::class, 'store'])
            ->name('password.store');
    });

    // StoreOwner Authenticated Routes
    Route::middleware('auth.storeowner')->group(function () {
        Route::get('/', function () {
            return view('storeowner.dashboard');
        })->name('dashboard');

        Route::put('password', [StoreOwnerPasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [StoreOwnerAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [StoreOwnerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [StoreOwnerProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [StoreOwnerProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

