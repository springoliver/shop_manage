<?php

use App\Http\Admin\Controllers\DashboardController;
use App\Http\Admin\Controllers\DepartmentController;
use App\Http\Admin\Controllers\EmailFormatController;
use App\Http\Admin\Controllers\ModuleController;
use App\Http\Admin\Controllers\RequestedModuleController;
use App\Http\Admin\Controllers\PageController;
use App\Http\Admin\Controllers\ProfileController as AdminProfileController;
use App\Http\Admin\Controllers\SettingController;
use App\Http\Admin\Controllers\StoreController;
use App\Http\Admin\Controllers\StoreOwnerController;
use App\Http\Admin\Controllers\StoreTypeController;
use App\Http\Admin\Controllers\UserGroupController;
use App\Http\Admin\Controllers\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Admin\Controllers\Auth\ConfirmablePasswordController as AdminConfirmablePasswordController;
use App\Http\Admin\Controllers\Auth\EmailVerificationNotificationController as AdminEmailVerificationNotificationController;
use App\Http\Admin\Controllers\Auth\EmailVerificationPromptController as AdminEmailVerificationPromptController;
use App\Http\Admin\Controllers\Auth\NewPasswordController as AdminNewPasswordController;
use App\Http\Admin\Controllers\Auth\PasswordController as AdminPasswordController;
use App\Http\Admin\Controllers\Auth\PasswordResetLinkController as AdminPasswordResetLinkController;
use App\Http\Admin\Controllers\Auth\RegisteredUserController as AdminRegisteredUserController;
use App\Http\Admin\Controllers\Auth\VerifyEmailController as AdminVerifyEmailController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Guest Routes
    Route::middleware('guest.admin')->group(function () {
        Route::get('register', [AdminRegisteredUserController::class, 'create'])
            ->name('register');

        Route::post('register', [AdminRegisteredUserController::class, 'store']);

        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [AdminPasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [AdminPasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [AdminNewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [AdminNewPasswordController::class, 'store'])
            ->name('password.store');
    });

    // Admin Authenticated Routes
    Route::middleware('auth.admin')->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::match(['get', 'post'], 'dashboard/owner/{utype?}', [DashboardController::class, 'owner'])->name('dashboard.owner');

        Route::get('verify-email', AdminEmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', AdminVerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [AdminEmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [AdminConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [AdminConfirmablePasswordController::class, 'store']);

        Route::put('password', [AdminPasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

        // Store Owners Management
        Route::get('store-owners', [StoreOwnerController::class, 'index'])->name('store-owners.index');
        Route::get('store-owners/create', [StoreOwnerController::class, 'create'])->name('store-owners.create');
        Route::post('store-owners', [StoreOwnerController::class, 'store'])->name('store-owners.store');
        Route::get('store-owners/{storeOwner}', [StoreOwnerController::class, 'show'])->name('store-owners.show');
        Route::get('store-owners/{storeOwner}/edit', [StoreOwnerController::class, 'edit'])->name('store-owners.edit');
        Route::put('store-owners/{storeOwner}', [StoreOwnerController::class, 'update'])->name('store-owners.update');
        Route::delete('store-owners/{storeOwner}', [StoreOwnerController::class, 'destroy'])->name('store-owners.destroy');
        Route::post('store-owners/{storeOwner}/change-status', [StoreOwnerController::class, 'changeStatus'])->name('store-owners.change-status');

        // Stores Management
        Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
        Route::get('stores/create', [StoreController::class, 'create'])->name('stores.create');
        Route::post('stores', [StoreController::class, 'store'])->name('stores.store');
        Route::get('stores/{store}', [StoreController::class, 'show'])->name('stores.show');
        Route::get('stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
        Route::put('stores/{store}', [StoreController::class, 'update'])->name('stores.update');
        Route::delete('stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');
        Route::post('stores/{store}/change-status', [StoreController::class, 'changeStatus'])->name('stores.change-status');

        // Store Types Management
        Route::get('store-types', [StoreTypeController::class, 'index'])->name('store-types.index');
        Route::get('store-types/create', [StoreTypeController::class, 'create'])->name('store-types.create');
        Route::post('store-types', [StoreTypeController::class, 'store'])->name('store-types.store');
        Route::get('store-types/{store_type}/edit', [StoreTypeController::class, 'edit'])->name('store-types.edit');
        Route::put('store-types/{store_type}', [StoreTypeController::class, 'update'])->name('store-types.update');
        Route::delete('store-types/{store_type}', [StoreTypeController::class, 'destroy'])->name('store-types.destroy');
        Route::post('store-types/{store_type}/change-status', [StoreTypeController::class, 'changeStatus'])->name('store-types.change-status');

        // Settings Management
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('settings/{setting}/edit', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings/{setting}', [SettingController::class, 'update'])->name('settings.update');

        // Pages Management
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::post('pages/{page}/change-status', [PageController::class, 'changeStatus'])->name('pages.change-status');

        // Modules Management
        Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::get('modules/create', [ModuleController::class, 'create'])->name('modules.create');
        Route::post('modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::get('modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
        Route::put('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
        Route::post('modules/{module}/change-status', [ModuleController::class, 'changeStatus'])->name('modules.change-status');

        // Requested Modules Management
        Route::get('requested-modules', [RequestedModuleController::class, 'index'])->name('requested-modules.index');
        Route::post('requested-modules/{requested_module}/change-status', [RequestedModuleController::class, 'changeStatus'])->name('requested-modules.change-status');

        // Email Formats Management
        Route::get('email-formats', [EmailFormatController::class, 'index'])->name('email-formats.index');
        Route::get('email-formats/{emailFormat}/edit', [EmailFormatController::class, 'edit'])->name('email-formats.edit');
        Route::put('email-formats/{emailFormat}', [EmailFormatController::class, 'update'])->name('email-formats.update');

        // User Groups Management
        Route::get('user-groups', [UserGroupController::class, 'index'])->name('user-groups.index');
        Route::get('user-groups/create', [UserGroupController::class, 'create'])->name('user-groups.create');
        Route::post('user-groups', [UserGroupController::class, 'store'])->name('user-groups.store');
        Route::get('user-groups/{userGroup}', [UserGroupController::class, 'show'])->name('user-groups.show');
        Route::get('user-groups/{userGroup}/edit', [UserGroupController::class, 'edit'])->name('user-groups.edit');
        Route::put('user-groups/{userGroup}', [UserGroupController::class, 'update'])->name('user-groups.update');
        Route::delete('user-groups/{userGroup}', [UserGroupController::class, 'destroy'])->name('user-groups.destroy');
        Route::post('user-groups/{userGroup}/change-status', [UserGroupController::class, 'changeStatus'])->name('user-groups.change-status');
        Route::post('user-groups/check-groupname', [UserGroupController::class, 'checkGroupNameAvailability'])->name('user-groups.check-groupname');

        // Departments Management
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::post('departments/{department}/change-status', [DepartmentController::class, 'changeStatus'])->name('departments.change-status');
        Route::post('departments/check-department', [DepartmentController::class, 'checkDepartmentAvailability'])->name('departments.check-department');
    });
});
