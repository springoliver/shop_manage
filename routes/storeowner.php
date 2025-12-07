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
use App\Http\StoreOwner\Controllers\EmployeeController as StoreOwnerEmployeeController;
use App\Http\StoreOwner\Controllers\RosterController as StoreOwnerRosterController;
use App\Http\StoreOwner\Controllers\HolidayRequestController as StoreOwnerHolidayRequestController;
use App\Http\StoreOwner\Controllers\ResignationController as StoreOwnerResignationController;
use App\Http\StoreOwner\Controllers\ClockTimeController as StoreOwnerClockTimeController;
use App\Http\StoreOwner\Controllers\DocumentController as StoreOwnerDocumentController;
use App\Http\StoreOwner\Controllers\EmployeePayrollController as StoreOwnerEmployeePayrollController;
use App\Http\StoreOwner\Controllers\EmployeeReviewsController as StoreOwnerEmployeeReviewsController;
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

        // Employee routes
        Route::get('employee', [StoreOwnerEmployeeController::class, 'index'])->name('employee.index');
        Route::get('employee/create', [StoreOwnerEmployeeController::class, 'create'])->name('employee.create');
        Route::post('employee', [StoreOwnerEmployeeController::class, 'store'])->name('employee.store');
        Route::get('employee/{employeeid}', [StoreOwnerEmployeeController::class, 'show'])->name('employee.show');
        Route::get('employee/{employeeid}/edit', [StoreOwnerEmployeeController::class, 'edit'])->name('employee.edit');
        Route::put('employee/{employeeid}', [StoreOwnerEmployeeController::class, 'update'])->name('employee.update');
        Route::delete('employee/{employeeid}', [StoreOwnerEmployeeController::class, 'destroy'])->name('employee.destroy');
        Route::post('employee/change-status', [StoreOwnerEmployeeController::class, 'changeStatus'])->name('employee.change-status');
        Route::post('employee/check-email', [StoreOwnerEmployeeController::class, 'checkEmail'])->name('employee.check-email');
        Route::post('employee/check-username', [StoreOwnerEmployeeController::class, 'checkUsername'])->name('employee.check-username');

        // Module Setting routes
        Route::get('modulesetting', [StoreOwnerModuleSettingController::class, 'index'])->name('modulesetting.index');
        Route::post('modulesetting/view', [StoreOwnerModuleSettingController::class, 'view'])->name('modulesetting.view');
        Route::get('modulesetting/edit/{usergroupid}', [StoreOwnerModuleSettingController::class, 'edit'])->name('modulesetting.edit');
        Route::post('modulesetting/update', [StoreOwnerModuleSettingController::class, 'update'])->name('modulesetting.update');
        Route::post('modulesetting/install', [StoreOwnerModuleSettingController::class, 'install'])->name('modulesetting.install');

        // Roster routes (Base Roster)
        Route::get('roster', [StoreOwnerRosterController::class, 'index'])->name('roster.index');
        Route::get('roster/dept/{departmentid}', [StoreOwnerRosterController::class, 'indexDept'])->name('roster.index-dept');
        Route::get('roster/create/{employeeid}', [StoreOwnerRosterController::class, 'create'])->name('roster.create');
        Route::post('roster', [StoreOwnerRosterController::class, 'store'])->name('roster.store');
        Route::get('roster/{employeeid}/edit', [StoreOwnerRosterController::class, 'edit'])->name('roster.edit');
        Route::put('roster/{employeeid}', [StoreOwnerRosterController::class, 'update'])->name('roster.update');
        Route::delete('roster/{employeeid}', [StoreOwnerRosterController::class, 'destroy'])->name('roster.destroy');
        Route::get('roster/{employeeid}/view', [StoreOwnerRosterController::class, 'view'])->name('roster.view');

        // Roster routes (Weekly Roster)
        Route::get('roster/week', [StoreOwnerRosterController::class, 'weekroster'])->name('roster.weekroster');
        Route::post('roster/week/add', [StoreOwnerRosterController::class, 'weekrosteradd'])->name('roster.weekrosteradd');
        Route::get('roster/week/{weekid}', [StoreOwnerRosterController::class, 'viewweekroster'])->name('roster.viewweekroster');
        Route::get('roster/week/{weekid}/dept/{departmentid?}', [StoreOwnerRosterController::class, 'rosterforweek'])->name('roster.rosterforweek');
        Route::get('roster/week/{weekid}/employee/{employeeid}/edit', [StoreOwnerRosterController::class, 'editweekroster'])->name('roster.editweekroster');
        Route::put('roster/week/{weekid}/employee/{employeeid}', [StoreOwnerRosterController::class, 'updateweekroster'])->name('roster.updateweekroster');
        Route::delete('roster/week/{weekid}/employee/{employeeid}', [StoreOwnerRosterController::class, 'deleterosterweek'])->name('roster.deleterosterweek');

        // Holiday Request routes (specific routes must come before parameterized routes)
        Route::get('holidayrequest', [StoreOwnerHolidayRequestController::class, 'index'])->name('holidayrequest.index');
        Route::get('holidayrequest/create', [StoreOwnerHolidayRequestController::class, 'create'])->name('holidayrequest.create');
        Route::get('holidayrequest/calenderview', [StoreOwnerHolidayRequestController::class, 'calendarView'])->name('holidayrequest.calenderview');
        Route::get('holidayrequest/get-requests', [StoreOwnerHolidayRequestController::class, 'getRequests'])->name('holidayrequest.get-requests');
        Route::get('holidayrequest/type/{type}', [StoreOwnerHolidayRequestController::class, 'getRequestByType'])->name('holidayrequest.type');
        Route::post('holidayrequest', [StoreOwnerHolidayRequestController::class, 'store'])->name('holidayrequest.store');
        Route::post('holidayrequest/change-status', [StoreOwnerHolidayRequestController::class, 'changeStatus'])->name('holidayrequest.change-status');
        Route::post('holidayrequest/view-request', [StoreOwnerHolidayRequestController::class, 'viewRequest'])->name('holidayrequest.view-request');
        Route::post('holidayrequest/search', [StoreOwnerHolidayRequestController::class, 'search'])->name('holidayrequest.search');
        Route::get('holidayrequest/{requestid}/edit', [StoreOwnerHolidayRequestController::class, 'edit'])->name('holidayrequest.edit');
        Route::get('holidayrequest/{requestid}', [StoreOwnerHolidayRequestController::class, 'show'])->name('holidayrequest.show');
        Route::put('holidayrequest/{requestid}', [StoreOwnerHolidayRequestController::class, 'update'])->name('holidayrequest.update');
        Route::delete('holidayrequest/{requestid}', [StoreOwnerHolidayRequestController::class, 'destroy'])->name('holidayrequest.destroy');

        // Resignation routes (specific routes must come before parameterized routes)
        Route::get('resignation', [StoreOwnerResignationController::class, 'index'])->name('resignation.index');
        Route::get('resignation/type/{type}', [StoreOwnerResignationController::class, 'getResignationByType'])->name('resignation.type');
        Route::post('resignation/change-status', [StoreOwnerResignationController::class, 'changeStatus'])->name('resignation.change-status');
        Route::post('resignation/search', [StoreOwnerResignationController::class, 'search'])->name('resignation.search');
        Route::get('resignation/{resignationid}', [StoreOwnerResignationController::class, 'view'])->name('resignation.view');
        Route::delete('resignation/{resignationid}', [StoreOwnerResignationController::class, 'destroy'])->name('resignation.destroy');

        // Clock Time routes
        Route::get('clocktime', [StoreOwnerClockTimeController::class, 'index'])->name('clocktime.index');
        Route::post('clocktime/clockreport', [StoreOwnerClockTimeController::class, 'clockReport'])->name('clocktime.clockreport');
        Route::post('clocktime/exportpdf', [StoreOwnerClockTimeController::class, 'exportPdf'])->name('clocktime.exportpdf');
        Route::get('clocktime/employee_holidays', [StoreOwnerClockTimeController::class, 'employeeHolidays'])->name('clocktime.employee_holidays');
        Route::get('clocktime/compare_weekly_hrs', [StoreOwnerClockTimeController::class, 'compareWeeklyHrs'])->name('clocktime.compare_weekly_hrs');
        Route::get('clocktime/allemployee_weeklyhrs', [StoreOwnerClockTimeController::class, 'allemployeeWeeklyhrs'])->name('clocktime.allemployee_weeklyhrs');
        Route::get('clocktime/monthly_hrs_allemployee', [StoreOwnerClockTimeController::class, 'monthlyHrsAllEmployee'])->name('clocktime.monthly_hrs_allemployee');

        // Document routes
        Route::get('document', [StoreOwnerDocumentController::class, 'index'])->name('document.index');
        Route::get('document/add', [StoreOwnerDocumentController::class, 'create'])->name('document.create');
        Route::post('document', [StoreOwnerDocumentController::class, 'store'])->name('document.store');
        Route::post('document/get-documents', [StoreOwnerDocumentController::class, 'getDocuments'])->name('document.get-documents');
        Route::delete('document/{docid}', [StoreOwnerDocumentController::class, 'destroy'])->name('document.destroy');

        // Employee Payroll routes
        Route::get('employeepayroll', [StoreOwnerEmployeePayrollController::class, 'index'])->name('employeepayroll.index');
        Route::get('employeepayroll/payslipsby_employee/{employeeid}', [StoreOwnerEmployeePayrollController::class, 'payslipsByEmployee'])->name('employeepayroll.payslipsby-employee');
        Route::get('employeepayroll/addpayslip', [StoreOwnerEmployeePayrollController::class, 'addPayslip'])->name('employeepayroll.addpayslip');
        Route::post('employeepayroll/storepayslip', [StoreOwnerEmployeePayrollController::class, 'storePayslip'])->name('employeepayroll.storepayslip');
        Route::get('employeepayroll/view/{payslipid}', [StoreOwnerEmployeePayrollController::class, 'view'])->name('employeepayroll.view');
        Route::get('employeepayroll/downloadpdf/{id}', [StoreOwnerEmployeePayrollController::class, 'downloadPdf'])->name('employeepayroll.downloadpdf');
        Route::delete('employeepayroll/{payslipid}', [StoreOwnerEmployeePayrollController::class, 'destroy'])->name('employeepayroll.destroy');
        Route::get('employeepayroll/process_payroll', [StoreOwnerEmployeePayrollController::class, 'processPayroll'])->name('employeepayroll.process-payroll');
        Route::post('employeepayroll/get-week-details', [StoreOwnerEmployeePayrollController::class, 'getWeekDetails'])->name('employeepayroll.get-week-details');
        Route::get('employeepayroll/employee-settings', function() {
            return view('storeowner.employeepayroll.employee_settings');
        })->name('employeepayroll.employee-settings');

        // Employee Reviews routes
        Route::get('employeereviews', [StoreOwnerEmployeeReviewsController::class, 'index'])->name('employeereviews.index');
        Route::get('employeereviews/all_reviews', [StoreOwnerEmployeeReviewsController::class, 'allReviews'])->name('employeereviews.all-reviews');
        Route::get('employeereviews/due_reviews', [StoreOwnerEmployeeReviewsController::class, 'dueReviews'])->name('employeereviews.due-reviews');
        Route::get('employeereviews/reviews_by_employee/{employeeid}', [StoreOwnerEmployeeReviewsController::class, 'reviewsByEmployee'])->name('employeereviews.reviews-by-employee');
        Route::get('employeereviews/add_review/{employeeid}', [StoreOwnerEmployeeReviewsController::class, 'addReview'])->name('employeereviews.add-review');
        Route::post('employeereviews/insert_review', [StoreOwnerEmployeeReviewsController::class, 'insertReview'])->name('employeereviews.insert-review');
        Route::get('employeereviews/edit_review/{emp_reviewid}', [StoreOwnerEmployeeReviewsController::class, 'editReview'])->name('employeereviews.edit-review');
        Route::post('employeereviews/update_review', [StoreOwnerEmployeeReviewsController::class, 'updateReview'])->name('employeereviews.update-review');
        Route::get('employeereviews/view/{emp_reviewid}', [StoreOwnerEmployeeReviewsController::class, 'view'])->name('employeereviews.view');
        Route::delete('employeereviews/{emp_reviewid}', [StoreOwnerEmployeeReviewsController::class, 'destroy'])->name('employeereviews.destroy');
        Route::get('employeereviews/review_subjects', [StoreOwnerEmployeeReviewsController::class, 'reviewSubjects'])->name('employeereviews.review-subjects');
        Route::get('employeereviews/add_review_subject', [StoreOwnerEmployeeReviewsController::class, 'addReviewSubject'])->name('employeereviews.add-review-subject');
        Route::post('employeereviews/update_review_subject', [StoreOwnerEmployeeReviewsController::class, 'updateReviewSubject'])->name('employeereviews.update-review-subject');
        Route::get('employeereviews/edit_review_subject/{review_subjectid}', [StoreOwnerEmployeeReviewsController::class, 'editReviewSubject'])->name('employeereviews.edit-review-subject');
        Route::delete('employeereviews/review_subject/{review_subjectid}', [StoreOwnerEmployeeReviewsController::class, 'destroyReviewSubject'])->name('employeereviews.destroy-review-subject');
        Route::post('employeereviews/change_review_subject_status', [StoreOwnerEmployeeReviewsController::class, 'changeReviewSubjectStatus'])->name('employeereviews.change-review-subject-status');

        Route::put('password', [StoreOwnerPasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [StoreOwnerAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [StoreOwnerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [StoreOwnerProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [StoreOwnerProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

