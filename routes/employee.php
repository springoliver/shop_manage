<?php

use App\Http\Employee\Controllers\ProfileController as EmployeeProfileController;
use App\Http\Employee\Controllers\RosterController as EmployeeRosterController;
use App\Http\Employee\Controllers\HolidayRequestController as EmployeeHolidayRequestController;
use App\Http\Employee\Controllers\PayrollController as EmployeePayrollController;
use App\Http\Employee\Controllers\ResignationController as EmployeeResignationController;
use App\Http\Employee\Controllers\DocumentController as EmployeeDocumentController;
use App\Http\Employee\Controllers\RequestModuleController as EmployeeRequestModuleController;
use App\Http\Employee\Controllers\PosController as EmployeePosController;
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

        // Profile - View (matches CI's my_profile/index)
        Route::get('profile', [EmployeeProfileController::class, 'index'])->name('profile.index');
        
        // Profile - Edit (matches CI's dashboard/editprofile)
        Route::get('profile/edit', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [EmployeeProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [EmployeeProfileController::class, 'destroy'])->name('profile.destroy');
        
        // AJAX Validation
        Route::post('profile/check-email', [EmployeeProfileController::class, 'checkEmailExists'])->name('profile.check-email');
        Route::post('profile/check-username', [EmployeeProfileController::class, 'checkUsernameExists'])->name('profile.check-username');

        // My Roster (matches CI's my_profile/roster_list)
        Route::get('roster', [EmployeeRosterController::class, 'index'])->name('roster.index');
        Route::get('roster/{storeid}/{employeeid}/{weekid}', [EmployeeRosterController::class, 'show'])->name('roster.show');
        Route::post('roster/navigate', [EmployeeRosterController::class, 'navigate'])->name('roster.navigate');

        // Time Off Request (matches CI's my_profile/time_of_request)
        Route::get('holidayrequest', [EmployeeHolidayRequestController::class, 'index'])->name('holidayrequest.index');
        Route::get('holidayrequest/calenderview', [EmployeeHolidayRequestController::class, 'calendarView'])->name('holidayrequest.calenderview');
        Route::get('holidayrequest/get-requests', [EmployeeHolidayRequestController::class, 'getRequests'])->name('holidayrequest.get-requests');
        Route::get('holidayrequest/create', [EmployeeHolidayRequestController::class, 'create'])->name('holidayrequest.create');
        Route::post('holidayrequest', [EmployeeHolidayRequestController::class, 'store'])->name('holidayrequest.store');
        Route::get('holidayrequest/{requestid}', [EmployeeHolidayRequestController::class, 'show'])->name('holidayrequest.show');
        Route::get('holidayrequest/{requestid}/edit', [EmployeeHolidayRequestController::class, 'edit'])->name('holidayrequest.edit');
        Route::put('holidayrequest/{requestid}', [EmployeeHolidayRequestController::class, 'update'])->name('holidayrequest.update');
        Route::delete('holidayrequest/{requestid}', [EmployeeHolidayRequestController::class, 'destroy'])->name('holidayrequest.destroy');

        // My Payroll (matches CI's my_profile/payroll_list)
        Route::get('payroll', [EmployeePayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{storeid}/{employeeid}/{weekid}', [EmployeePayrollController::class, 'show'])->name('payroll.show');
        Route::get('payroll/download-pdf/{payslipid}', [EmployeePayrollController::class, 'downloadPdf'])->name('payroll.download-pdf');

        // Resignation (matches CI's my_profile/resignation)
        Route::get('resignation', [EmployeeResignationController::class, 'index'])->name('resignation.index');
        Route::get('resignation/create', [EmployeeResignationController::class, 'create'])->name('resignation.create');
        Route::post('resignation', [EmployeeResignationController::class, 'store'])->name('resignation.store');
        Route::get('resignation/{resignationid}', [EmployeeResignationController::class, 'show'])->name('resignation.show');

        // My Documents (matches CI's my_profile/view_document)
        Route::get('document', [EmployeeDocumentController::class, 'index'])->name('document.index');
        Route::get('document/download/{docid}', [EmployeeDocumentController::class, 'download'])->name('document.download');

        // Suggest a new module (matches CI's requestmodule)
        Route::get('requestmodule', [EmployeeRequestModuleController::class, 'index'])->name('requestmodule.index');
        Route::get('requestmodule/add', [EmployeeRequestModuleController::class, 'create'])->name('requestmodule.create');
        Route::post('requestmodule', [EmployeeRequestModuleController::class, 'store'])->name('requestmodule.store');
        Route::get('requestmodule/view/{rmid}', [EmployeeRequestModuleController::class, 'show'])->name('requestmodule.show');
        Route::post('requestmodule/check-module-name', [EmployeeRequestModuleController::class, 'checkModuleName'])->name('requestmodule.check-module-name');

        // POS (Point Of Sale) (matches CI's pos)
        Route::get('pos', [EmployeePosController::class, 'index'])->name('pos.index');
        Route::get('pos/main', [EmployeePosController::class, 'main'])->name('pos.main');
    });
});
