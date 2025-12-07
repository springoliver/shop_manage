<?php

use App\Http\Employee\Controllers\ProfileController as EmployeeProfileController;
use App\Http\Employee\Controllers\Auth\AuthenticatedSessionController as EmployeeAuthenticatedSessionController;
use App\Http\Employee\Controllers\Auth\ConfirmablePasswordController as EmployeeConfirmablePasswordController;
use App\Http\Employee\Controllers\Auth\EmailVerificationNotificationController as EmployeeEmailVerificationNotificationController;
use App\Http\Employee\Controllers\Auth\EmailVerificationPromptController as EmployeeEmailVerificationPromptController;
use App\Http\Employee\Controllers\Auth\NewPasswordController as EmployeeNewPasswordController;
use App\Http\Employee\Controllers\Auth\PasswordController as EmployeePasswordController;
use App\Http\Employee\Controllers\Auth\PasswordResetLinkController as EmployeePasswordResetLinkController;
use App\Http\Employee\Controllers\Auth\RegisteredUserController as EmployeeRegisteredUserController;
use App\Http\Employee\Controllers\Auth\VerifyEmailController as EmployeeVerifyEmailController;
use Illuminate\Support\Facades\Route;

// Employee Routes
Route::prefix('employees')->name('employee.')->group(function () {
    // Employee Guest Routes
    Route::middleware('guest.employee')->group(function () {
        Route::get('register', [EmployeeRegisteredUserController::class, 'create'])
            ->name('register');

        Route::post('register', [EmployeeRegisteredUserController::class, 'store']);

        Route::get('login', [EmployeeAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [EmployeeAuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [EmployeePasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [EmployeePasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [EmployeeNewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [EmployeeNewPasswordController::class, 'store'])
            ->name('password.store');
    });

    // Employee Authenticated Routes
    Route::middleware('auth.employee')->group(function () {
        Route::get('/', function () {
            return view('employee.dashboard');
        })->name('dashboard');

        Route::get('verify-email', EmployeeEmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', EmployeeVerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmployeeEmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [EmployeeConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [EmployeeConfirmablePasswordController::class, 'store']);

        Route::put('password', [EmployeePasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [EmployeeAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [EmployeeProfileController::class, 'destroy'])->name('profile.destroy');
    });
});
