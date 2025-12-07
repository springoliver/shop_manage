<?php

use App\Http\StoreOwner\Controllers\ProfileController as StoreOwnerProfileController;
use App\Http\StoreOwner\Controllers\Auth\ActivationController as StoreOwnerActivationController;
use App\Http\StoreOwner\Controllers\Auth\AuthenticatedSessionController as StoreOwnerAuthenticatedSessionController;
use App\Http\StoreOwner\Controllers\Auth\NewPasswordController as StoreOwnerNewPasswordController;
use App\Http\StoreOwner\Controllers\Auth\PasswordController as StoreOwnerPasswordController;
use App\Http\StoreOwner\Controllers\Auth\PasswordResetLinkController as StoreOwnerPasswordResetLinkController;
use App\Http\StoreOwner\Controllers\Auth\RegisteredUserController as StoreOwnerRegisteredUserController;
use App\Http\StoreOwner\Controllers\DashboardController as StoreOwnerDashboardController;
use App\Http\StoreOwner\Controllers\StoreController as StoreOwnerStoreController;
use App\Http\StoreOwner\Controllers\UserGroupController as StoreOwnerUserGroupController;
use App\Http\StoreOwner\Controllers\DepartmentController as StoreOwnerDepartmentController;
use App\Http\StoreOwner\Controllers\ModuleSettingController as StoreOwnerModuleSettingController;
use Illuminate\Support\Facades\Route;

// StoreOwner Routes (Default End Users)
Route::name('storeowner.')->group(function () {
    // StoreOwner Guest Routes
    Route::middleware('guest.storeowner')->group(function () {
        Route::get('login', [StoreOwnerAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [StoreOwnerAuthenticatedSessionController::class, 'store']);

        Route::get('register', [StoreOwnerRegisteredUserController::class, 'create'])
            ->name('register');

        Route::post('register', [StoreOwnerRegisteredUserController::class, 'store']);

        Route::get('register/store', [StoreOwnerRegisteredUserController::class, 'createStore'])
            ->name('register.store');

        Route::post('register/store', [StoreOwnerRegisteredUserController::class, 'storeRegister']);

        Route::get('activate/{token}', [StoreOwnerActivationController::class, 'activate'])
            ->name('activate');

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
        Route::get('/', [StoreOwnerDashboardController::class, 'index'])->name('dashboard');

        // Store routes
        Route::get('store', [StoreOwnerStoreController::class, 'index'])->name('store.index');
        Route::get('store/create', [StoreOwnerStoreController::class, 'create'])->name('store.create');
        Route::post('store', [StoreOwnerStoreController::class, 'store'])->name('store.store');
        Route::get('store/{store}/edit', [StoreOwnerStoreController::class, 'edit'])->name('store.edit');
        Route::get('store/{store}', [StoreOwnerStoreController::class, 'show'])->name('store.show');
        Route::put('store/{store}', [StoreOwnerStoreController::class, 'update'])->name('store.update');
        Route::post('store/updatestoreinfo', [StoreOwnerStoreController::class, 'updateStoreInfo'])->name('store.update-info');
        Route::delete('store/{store}', [StoreOwnerStoreController::class, 'destroy'])->name('store.destroy');
        Route::post('store/{store}/change-status', [StoreOwnerStoreController::class, 'changeStatus'])->name('store.change-status');
        Route::post('store/check-storename', [StoreOwnerStoreController::class, 'checkStoreName'])->name('store.check-name');
        Route::post('store/check-storeemail', [StoreOwnerStoreController::class, 'checkStoreEmail'])->name('store.check-email');
        Route::post('store/change', [StoreOwnerStoreController::class, 'changeStore'])->name('store.change');

        // User Group routes
        Route::get('usergroup', [StoreOwnerUserGroupController::class, 'index'])->name('usergroup.index');
        Route::get('usergroup/create', [StoreOwnerUserGroupController::class, 'create'])->name('usergroup.create');
        Route::post('usergroup', [StoreOwnerUserGroupController::class, 'store'])->name('usergroup.store');
        Route::get('usergroup/{usergroup:usergroupid}/edit', [StoreOwnerUserGroupController::class, 'edit'])->name('usergroup.edit');
        Route::put('usergroup/{usergroup:usergroupid}', [StoreOwnerUserGroupController::class, 'update'])->name('usergroup.update');
        Route::delete('usergroup/{usergroup:usergroupid}', [StoreOwnerUserGroupController::class, 'destroy'])->name('usergroup.destroy');
        Route::post('usergroup/view', [StoreOwnerUserGroupController::class, 'view'])->name('usergroup.view');
        Route::post('usergroup/check-name', [StoreOwnerUserGroupController::class, 'checkName'])->name('usergroup.check-name');

        // Department routes
        Route::get('department', [StoreOwnerDepartmentController::class, 'index'])->name('department.index');
        Route::get('department/create', [StoreOwnerDepartmentController::class, 'create'])->name('department.create');
        Route::post('department', [StoreOwnerDepartmentController::class, 'store'])->name('department.store');
        Route::get('department/{departmentid}/edit', [StoreOwnerDepartmentController::class, 'edit'])->name('department.edit');
        Route::put('department/{departmentid}', [StoreOwnerDepartmentController::class, 'update'])->name('department.update');
        Route::delete('department/{departmentid}', [StoreOwnerDepartmentController::class, 'destroy'])->name('department.destroy');
        Route::post('department/change-status', [StoreOwnerDepartmentController::class, 'changeStatus'])->name('department.change-status');
        Route::post('department/check-name', [StoreOwnerDepartmentController::class, 'checkName'])->name('department.check-name');

        // Module Setting routes
        Route::get('modulesetting', [StoreOwnerModuleSettingController::class, 'index'])->name('modulesetting.index');
        Route::post('modulesetting/view', [StoreOwnerModuleSettingController::class, 'view'])->name('modulesetting.view');
        Route::get('modulesetting/edit/{usergroupid}', [StoreOwnerModuleSettingController::class, 'edit'])->name('modulesetting.edit');
        Route::post('modulesetting/update', [StoreOwnerModuleSettingController::class, 'update'])->name('modulesetting.update');
        Route::post('modulesetting/install', [StoreOwnerModuleSettingController::class, 'install'])->name('modulesetting.install');

        Route::put('password', [StoreOwnerPasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [StoreOwnerAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [StoreOwnerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [StoreOwnerProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [StoreOwnerProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

