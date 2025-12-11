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
use App\Http\StoreOwner\Controllers\PosSettingController as StoreOwnerPosSettingController;
use App\Http\StoreOwner\Controllers\SupplierSettingController as StoreOwnerSupplierSettingController;
use App\Http\StoreOwner\Controllers\SupplierController as StoreOwnerSupplierController;
use App\Http\StoreOwner\Controllers\ProductController as StoreOwnerProductController;
use App\Http\StoreOwner\Controllers\OrderingSettingController as StoreOwnerOrderingSettingController;
use App\Http\StoreOwner\Controllers\OrderingController as StoreOwnerOrderingController;
use App\Http\StoreOwner\Controllers\AjaxController as StoreOwnerAjaxController;
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
        Route::post('/', [StoreOwnerDashboardController::class, 'index'])->name('dashboard.post');
        
        // Dashboard Settings AJAX
        Route::post('dashboard/settings', [StoreOwnerDashboardController::class, 'settings'])->name('dashboard.settings');
        Route::get('dashboard/getSettings', [StoreOwnerDashboardController::class, 'getSettings'])->name('dashboard.getSettings');

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
        Route::get('roster/viewweekroster', [StoreOwnerRosterController::class, 'viewweekroster'])->name('roster.viewweekroster');
        Route::get('roster/week/{weekid}', [StoreOwnerRosterController::class, 'viewweekroster'])->name('roster.viewweekroster.byid');
        Route::get('roster/week/{weekid}/dept/{departmentid?}', [StoreOwnerRosterController::class, 'rosterforweek'])->name('roster.rosterforweek');
        Route::delete('roster/week/{weekid}/employee/{employeeid}', [StoreOwnerRosterController::class, 'deleterosterweek'])->name('roster.deleterosterweek');
        Route::get('roster/print', [StoreOwnerRosterController::class, 'printviewroster'])->name('roster.printviewroster');
        Route::get('roster/search-print', [StoreOwnerRosterController::class, 'searchprintroster'])->name('roster.searchprintroster');
        Route::post('roster/search-print', [StoreOwnerRosterController::class, 'searchprintroster'])->name('roster.searchprintroster');
        Route::get('roster/search-week', [StoreOwnerRosterController::class, 'searchweekroster'])->name('roster.searchweekroster');
        Route::post('roster/search-week', [StoreOwnerRosterController::class, 'searchweekroster'])->name('roster.searchweekroster');
        Route::post('roster/add-edit-week', [StoreOwnerRosterController::class, 'addeditweekroster'])->name('roster.addeditweekroster');
        Route::get('roster/email', [StoreOwnerRosterController::class, 'emailRoster'])->name('roster.email');

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
        Route::get('clocktime/week-clock-time/{employeeid}/{date}', [StoreOwnerClockTimeController::class, 'weekClockTime'])->name('clocktime.week-clock-time');
        Route::get('clocktime/week-clock-time-allemp/{weekid}/{date}', [StoreOwnerClockTimeController::class, 'weekClockTimeAllEmp'])->name('clocktime.week-clock-time-allemp');
        Route::post('clocktime/edit-clock-inout', [StoreOwnerClockTimeController::class, 'editClockInOut'])->name('clocktime.edit-clock-inout');
        Route::post('clocktime/edit-emp-timecard', [StoreOwnerClockTimeController::class, 'editEmpTimecard'])->name('clocktime.edit-emp-timecard');
        Route::post('clocktime/add-shift', [StoreOwnerClockTimeController::class, 'addShift'])->name('clocktime.add-shift');
        Route::get('clocktime/delete-shift/{eltid}', [StoreOwnerClockTimeController::class, 'deleteShift'])->name('clocktime.delete-shift');
        Route::get('clocktime/generate-week-payslip', [StoreOwnerClockTimeController::class, 'generateWeekPayslip'])->name('clocktime.generate-week-payslip');
        Route::get('clocktime/export-week-allemp/{weekid}/{date}', [StoreOwnerClockTimeController::class, 'exportWeekAllEmp'])->name('clocktime.export-week-allemp');
        Route::post('clocktime/upload-allemployee-daily-hours', [StoreOwnerClockTimeController::class, 'uploadAllEmployeeDailyHours'])->name('clocktime.upload-allemployee-daily-hours');
        Route::get('clocktime/week-allemp-payroll-hrs/{weekid}/{date}', [StoreOwnerClockTimeController::class, 'weekAllEmpPayrollHrs'])->name('clocktime.week-allemp-payroll-hrs');
        Route::get('clocktime/export-payroll-hrs/{weekid}/{date}', [StoreOwnerClockTimeController::class, 'exportPayrollHrs'])->name('clocktime.export-payroll-hrs');
        Route::post('clocktime/upload-all-weekly-hours', [StoreOwnerClockTimeController::class, 'uploadAllWeeklyHours'])->name('clocktime.upload-all-weekly-hours');
        Route::get('clocktime/yearly-hrs-byemployee/{employeeid}', [StoreOwnerClockTimeController::class, 'yearlyHrsByEmployee'])->name('clocktime.yearly-hrs-byemployee');
        Route::get('clocktime/yearly-hrs-by-year-employee/{employeeid}/{year}', [StoreOwnerClockTimeController::class, 'yearlyHrsByYearEmployee'])->name('clocktime.yearly-hrs-by-year-employee');
        Route::get('clocktime/group-yearly-hrs-allemployee/{year}', [StoreOwnerClockTimeController::class, 'groupYearlyHrsAllEmployee'])->name('clocktime.group-yearly-hrs-all-employee');
        Route::get('clocktime/weekly-hrs-byemployee/{employeeid}', [StoreOwnerClockTimeController::class, 'weeklyHrsByEmployee'])->name('clocktime.weekly-hrs-byemployee');
        Route::get('clocktime/weekly-hrs-byweek/{weekno}/{year}', [StoreOwnerClockTimeController::class, 'weeklyHrsByWeek'])->name('clocktime.weekly-hrs-byweek');
        Route::get('clocktime/export-all-employee-hols', [StoreOwnerClockTimeController::class, 'exportAllEmployeeHols'])->name('clocktime.export-all-employee-hols');
        Route::get('clocktime/export-group-all-employee-hols/{year}', [StoreOwnerClockTimeController::class, 'exportGroupAllEmployeeHols'])->name('clocktime.export-group-all-employee-hols');

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

        // POS Settings routes
        Route::get('possetting', [StoreOwnerPosSettingController::class, 'index'])->name('possetting.index');
        
        // Sections
        Route::get('possetting/sections', [StoreOwnerPosSettingController::class, 'sections'])->name('possetting.sections');
        Route::get('possetting/sections/{pos_floor_section_id}/edit', [StoreOwnerPosSettingController::class, 'editSection'])->name('possetting.edit-section');
        Route::post('possetting/update_floor_sections', [StoreOwnerPosSettingController::class, 'updateFloorSections'])->name('possetting.update-floor-sections');
        Route::delete('possetting/sections/{pos_floor_section_id}', [StoreOwnerPosSettingController::class, 'deleteSection'])->name('possetting.delete-section');
        
        // Tables
        Route::get('possetting/tables', [StoreOwnerPosSettingController::class, 'tables'])->name('possetting.tables');
        Route::get('possetting/tables/{pos_floor_table_id}/edit', [StoreOwnerPosSettingController::class, 'editTable'])->name('possetting.edit-table');
        Route::post('possetting/update_floor_tables', [StoreOwnerPosSettingController::class, 'updateFloorTables'])->name('possetting.update-floor-tables');
        Route::delete('possetting/tables/{pos_floor_table_id}', [StoreOwnerPosSettingController::class, 'deleteTable'])->name('possetting.delete-table');
        
        // Floor Layout
        Route::get('possetting/floor_layout', [StoreOwnerPosSettingController::class, 'floorLayout'])->name('possetting.floor-layout');
        Route::get('possetting/floor_layout/{pos_floor_section_id}', [StoreOwnerPosSettingController::class, 'floorLayoutSectionsTables'])->name('possetting.floor-layout-section');
        
        // Printers
        Route::get('possetting/printers', [StoreOwnerPosSettingController::class, 'printers'])->name('possetting.printers');
        Route::get('possetting/printers/{pos_receiptprinters_id}/edit', [StoreOwnerPosSettingController::class, 'editPrinter'])->name('possetting.edit-printer');
        Route::post('possetting/update_printers', [StoreOwnerPosSettingController::class, 'updatePrinters'])->name('possetting.update-printers');
        Route::delete('possetting/printers/{pos_receiptprinters_id}', [StoreOwnerPosSettingController::class, 'deletePrinter'])->name('possetting.delete-printer');
        
        // Sales Types
        Route::get('possetting/sales_types', [StoreOwnerPosSettingController::class, 'salesTypes'])->name('possetting.sales-types');
        Route::get('possetting/sales_types/{pos_sales_types_id}/edit', [StoreOwnerPosSettingController::class, 'editSalesType'])->name('possetting.edit-sales-type');
        Route::post('possetting/update_sales_types', [StoreOwnerPosSettingController::class, 'updateSalesTypes'])->name('possetting.update-sales-types');
        Route::delete('possetting/sales_types/{pos_sales_types_id}', [StoreOwnerPosSettingController::class, 'deleteSalesType'])->name('possetting.delete-sales-type');
        
        // Payment Types
        Route::get('possetting/payment_types', [StoreOwnerPosSettingController::class, 'paymentTypes'])->name('possetting.payment-types');
        Route::get('possetting/payment_types/{pos_payment_types_id}/edit', [StoreOwnerPosSettingController::class, 'editPaymentType'])->name('possetting.edit-payment-type');
        Route::post('possetting/update_payment_types', [StoreOwnerPosSettingController::class, 'updatePaymentTypes'])->name('possetting.update-payment-types');
        Route::delete('possetting/payment_types/{pos_payment_types_id}', [StoreOwnerPosSettingController::class, 'deletePaymentType'])->name('possetting.delete-payment-type');
        
        // Refund Reasons
        Route::get('possetting/refund_reasons', [StoreOwnerPosSettingController::class, 'refundReasons'])->name('possetting.refund-reasons');
        Route::get('possetting/refund_reasons/{pos_refund_reason_id}/edit', [StoreOwnerPosSettingController::class, 'editRefundReason'])->name('possetting.edit-refund-reason');
        Route::post('possetting/update_refund_reasons', [StoreOwnerPosSettingController::class, 'updateRefundReasons'])->name('possetting.update-refund-reasons');
        Route::delete('possetting/refund_reasons/{pos_refund_reason_id}', [StoreOwnerPosSettingController::class, 'deleteRefundReason'])->name('possetting.delete-refund-reason');
        
        // Gratuity
        Route::get('possetting/graduity', [StoreOwnerPosSettingController::class, 'gratuity'])->name('possetting.gratuity');
        Route::get('possetting/graduity/{pos_graduity_id}/edit', [StoreOwnerPosSettingController::class, 'editGratuity'])->name('possetting.edit-gratuity');
        Route::post('possetting/update_graduity', [StoreOwnerPosSettingController::class, 'updateGratuity'])->name('possetting.update-gratuity');
        Route::delete('possetting/graduity/{pos_graduity_id}', [StoreOwnerPosSettingController::class, 'deleteGratuity'])->name('possetting.delete-gratuity');
        
        // Discounts
        Route::get('possetting/discounts', [StoreOwnerPosSettingController::class, 'discounts'])->name('possetting.discounts');
        Route::get('possetting/discounts/{pos_discount_id}/edit', [StoreOwnerPosSettingController::class, 'editDiscount'])->name('possetting.edit-discount');
        Route::post('possetting/update_discount', [StoreOwnerPosSettingController::class, 'updateDiscount'])->name('possetting.update-discount');
        Route::delete('possetting/discounts/{pos_discount_id}', [StoreOwnerPosSettingController::class, 'deleteDiscount'])->name('possetting.delete-discount');
        
        // Modifiers
        Route::get('possetting/modifiers', [StoreOwnerPosSettingController::class, 'modifiers'])->name('possetting.modifiers');
        Route::get('possetting/modifiers/{pos_modifiers_id}/edit', [StoreOwnerPosSettingController::class, 'editModifier'])->name('possetting.edit-modifier');
        Route::post('possetting/update_modifier', [StoreOwnerPosSettingController::class, 'updateModifier'])->name('possetting.update-modifier');
        Route::delete('possetting/modifiers/{pos_modifiers_id}', [StoreOwnerPosSettingController::class, 'deleteModifier'])->name('possetting.delete-modifier');

        // Supplier Settings routes
        Route::get('suppliers/settings', [StoreOwnerSupplierSettingController::class, 'settings'])->name('suppliers.settings');
        
        // Suppliers routes
        Route::get('suppliers', [StoreOwnerSupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/add', [StoreOwnerSupplierController::class, 'add'])->name('suppliers.add');
        Route::get('suppliers/{supplierid}/edit', [StoreOwnerSupplierController::class, 'edit'])->name('suppliers.edit');
        Route::post('suppliers/update', [StoreOwnerSupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('suppliers/{supplierid}', [StoreOwnerSupplierController::class, 'destroy'])->name('suppliers.destroy');
        Route::post('suppliers/change_supplier_status', [StoreOwnerSupplierController::class, 'changeStatus'])->name('suppliers.change-status');
        
        // Products routes
        Route::get('products', [StoreOwnerProductController::class, 'index'])->name('products.index');
        Route::get('products/by_supplier/{supplierid}', [StoreOwnerProductController::class, 'bySupplier'])->name('products.by-supplier');
        Route::get('products/add', [StoreOwnerProductController::class, 'add'])->name('products.add');
        Route::get('products/{productid}/edit', [StoreOwnerProductController::class, 'edit'])->name('products.edit');
        Route::post('products/update', [StoreOwnerProductController::class, 'update'])->name('products.update');
        Route::delete('products/{productid}', [StoreOwnerProductController::class, 'destroy'])->name('products.destroy');
        Route::post('products/change_product_status', [StoreOwnerProductController::class, 'changeStatus'])->name('products.change-status');
        Route::post('products/change_product_price', [StoreOwnerProductController::class, 'changePrice'])->name('products.change-price');
        
        // Ordering Settings routes
        Route::get('ordering/settings', [StoreOwnerOrderingSettingController::class, 'settings'])->name('ordering.settings');
        
        // Purchase Order Categories
        Route::post('ordering/settings/updatecategories', [StoreOwnerOrderingSettingController::class, 'updateCategory'])->name('ordering.update-category');
        Route::get('ordering/settings/category/{categoryid}/edit', [StoreOwnerOrderingSettingController::class, 'editCategory'])->name('ordering.edit-category');
        Route::delete('ordering/settings/category/{categoryid}', [StoreOwnerOrderingSettingController::class, 'deleteCategory'])->name('ordering.delete-category');
        
        // Supplier Document Types
        Route::post('ordering/settings/updatesuppdoctype', [StoreOwnerOrderingSettingController::class, 'updateDocType'])->name('ordering.update-doc-type');
        Route::get('ordering/settings/doc_type/{docs_type_id}/edit', [StoreOwnerOrderingSettingController::class, 'editDocType'])->name('ordering.edit-doc-type');
        Route::delete('ordering/settings/doc_type/{docs_type_id}', [StoreOwnerOrderingSettingController::class, 'deleteDocType'])->name('ordering.delete-doc-type');
        
        // Ordering routes
        Route::get('ordering', [StoreOwnerOrderingController::class, 'index'])->name('ordering.index');
        Route::get('ordering/order', [StoreOwnerOrderingController::class, 'order'])->name('ordering.order');
        Route::post('ordering/order', [StoreOwnerOrderingController::class, 'order'])->name('ordering.order.submit');
        Route::get('ordering/waiting_approval', [StoreOwnerOrderingController::class, 'waitingApproval'])->name('ordering.waiting_approval');
        Route::post('ordering/waiting_approval', [StoreOwnerOrderingController::class, 'waitingApproval'])->name('ordering.waiting_approval.submit');
        
        // Ordering Reports routes
        Route::get('ordering/report', [StoreOwnerOrderingController::class, 'report'])->name('ordering.report');
        Route::post('ordering/report', [StoreOwnerOrderingController::class, 'report'])->name('ordering.report.submit');
        Route::get('ordering/product_report', [StoreOwnerOrderingController::class, 'productReport'])->name('ordering.product_report');
        Route::post('ordering/product_report', [StoreOwnerOrderingController::class, 'productReport'])->name('ordering.product_report.submit');
        Route::get('ordering/missing_delivery_dockets', [StoreOwnerOrderingController::class, 'missingDeliveryDockets'])->name('ordering.missing_delivery_dockets');
        Route::post('ordering/missing_delivery_dockets', [StoreOwnerOrderingController::class, 'missingDeliveryDockets'])->name('ordering.missing_delivery_dockets.submit');
        Route::get('ordering/credit_notes', [StoreOwnerOrderingController::class, 'creditNotes'])->name('ordering.credit_notes');
        Route::post('ordering/credit_notes', [StoreOwnerOrderingController::class, 'creditNotes'])->name('ordering.credit_notes.submit');
        Route::post('ordering/update_delivery_dock_status', [StoreOwnerOrderingController::class, 'updateDeliveryDockStatus'])->name('ordering.update_delivery_dock_status');
        
        // Invoices & Tax routes
        Route::get('ordering/tax_analysis', [StoreOwnerOrderingController::class, 'taxAnalysis'])->name('ordering.tax_analysis');
        Route::get('ordering/add_invoice', [StoreOwnerOrderingController::class, 'addInvoice'])->name('ordering.add_invoice');
        Route::get('ordering/add_bills/{delivery_date}', [StoreOwnerOrderingController::class, 'addBills'])->name('ordering.add_bills');
        Route::get('ordering/edit_bills/{purchase_orders_id}/{delivery_date}', [StoreOwnerOrderingController::class, 'editBills'])->name('ordering.edit_bills');
        Route::post('ordering/new_bill', [StoreOwnerOrderingController::class, 'newBill'])->name('ordering.new_bill');
        
        // Chart Views
        Route::get('ordering/reports_chart_yearly', [StoreOwnerOrderingController::class, 'reportsChartYearly'])->name('ordering.reports_chart_yearly');
        Route::get('ordering/reports_chart_monthly', [StoreOwnerOrderingController::class, 'reportsChartMonthly'])->name('ordering.reports_chart_monthly');
        Route::get('ordering/reports_chart_weekly', [StoreOwnerOrderingController::class, 'reportsChartWeekly'])->name('ordering.reports_chart_weekly');
        
        // PO Reports Charts (separate from Invoices & Tax charts)
        Route::get('ordering/po_chart_yearly', [StoreOwnerOrderingController::class, 'poChartYearly'])->name('ordering.po_chart_yearly');
        Route::get('ordering/po_chart_monthly', [StoreOwnerOrderingController::class, 'poChartMonthly'])->name('ordering.po_chart_monthly');
        Route::get('ordering/po_chart_weekly', [StoreOwnerOrderingController::class, 'poChartWeekly'])->name('ordering.po_chart_weekly');
        
        // Chart Data AJAX routes
        Route::post('ordering/get_allreports_chart_yearly', [StoreOwnerOrderingController::class, 'getAllReportsChartYearly'])->name('ordering.get_allreports_chart_yearly');
        Route::post('ordering/get_allreports_chart_monthly', [StoreOwnerOrderingController::class, 'getAllReportsChartMonthly'])->name('ordering.get_allreports_chart_monthly');
        Route::post('ordering/get_allpo_chart_weekly', [StoreOwnerOrderingController::class, 'getAllPoChartWeekly'])->name('ordering.get_allpo_chart_weekly');
        
        // PO Reports Chart AJAX endpoints
        Route::post('ordering/get_po_chart_yearly', [StoreOwnerOrderingController::class, 'getPoChartYearly'])->name('ordering.get_po_chart_yearly');
        Route::post('ordering/get_po_chart_monthly', [StoreOwnerOrderingController::class, 'getPoChartMonthly'])->name('ordering.get_po_chart_monthly');
        Route::post('ordering/get_po_chart_weekly', [StoreOwnerOrderingController::class, 'getPoChartWeekly'])->name('ordering.get_po_chart_weekly');
        
        // Supplier Documents routes
        Route::get('ordering/index_supplier_doc', [StoreOwnerOrderingController::class, 'indexSupplierDoc'])->name('ordering.index_supplier_doc');
        Route::get('ordering/add_supplier_doc', [StoreOwnerOrderingController::class, 'addSupplierDoc'])->name('ordering.add_supplier_doc');
        Route::post('ordering/update_supplier_doc', [StoreOwnerOrderingController::class, 'updateSupplierDoc'])->name('ordering.update_supplier_doc');
        Route::get('ordering/delete_supplier_doc/{docid}', [StoreOwnerOrderingController::class, 'deleteSupplierDoc'])->name('ordering.delete_supplier_doc');
        Route::post('ordering/get_documents', [StoreOwnerOrderingController::class, 'getDocuments'])->name('ordering.get_documents');
        
        // Delete Purchase Order
        Route::delete('ordering/delete-po/{purchase_orders_id}', [StoreOwnerOrderingController::class, 'deletePurchaseOrder'])->name('ordering.delete-po');
        
        // AJAX routes
        Route::get('ajax/get_products_by_supplier_id', [StoreOwnerAjaxController::class, 'getProductsBySupplierId'])->name('ajax.products-by-supplier');
        Route::get('ajax/get_purchase_order_detail', [StoreOwnerAjaxController::class, 'getPurchaseOrderDetail'])->name('ajax.get-purchase-order-detail');
        Route::get('ajax/remove_purchase_order', [StoreOwnerAjaxController::class, 'removePurchaseOrder'])->name('ajax.remove-purchase-order');
        
        // Roster AJAX routes
        Route::post('ajax/get_roster_data', [StoreOwnerAjaxController::class, 'getRosterData'])->name('ajax.get-roster-data');
        Route::post('ajax/get_roster_template_data', [StoreOwnerAjaxController::class, 'getRosterTemplateData'])->name('ajax.get-roster-template-data');
        Route::post('ajax/get_roster_datas', [StoreOwnerAjaxController::class, 'getRosterDatas'])->name('ajax.get-roster-datas');
        Route::post('ajax/get_edit_employee_roster', [StoreOwnerAjaxController::class, 'getEditEmployeeRoster'])->name('ajax.get-edit-employee-roster');
        Route::post('ajax/check_employee_in_leave', [StoreOwnerAjaxController::class, 'checkEmployeeInLeave'])->name('ajax.check-employee-in-leave');
        Route::post('ajax/check_department_hour', [StoreOwnerAjaxController::class, 'checkDepartmentHour'])->name('ajax.check-department-hour');
        Route::post('ajax/check_department_modal_hour', [StoreOwnerAjaxController::class, 'checkDepartmentModalHour'])->name('ajax.check-department-modal-hour');
        
        // Catalog Product Groups
        Route::post('suppliers/settings/update_catalog_group', [StoreOwnerSupplierSettingController::class, 'updateCatalogGroup'])->name('suppliers.update-catalog-group');
        Route::get('suppliers/settings/catalog_group/{catalog_product_groupid}/edit', [StoreOwnerSupplierSettingController::class, 'editCatalogGroup'])->name('suppliers.edit-catalog-group');
        Route::delete('suppliers/settings/catalog_group/{catalog_product_groupid}', [StoreOwnerSupplierSettingController::class, 'deleteCatalogGroup'])->name('suppliers.delete-catalog-group');
        
        // Product Shipments
        Route::post('suppliers/settings/update_shipment', [StoreOwnerSupplierSettingController::class, 'updateShipment'])->name('suppliers.update-shipment');
        Route::get('suppliers/settings/shipment/{shipmentid}/edit', [StoreOwnerSupplierSettingController::class, 'editShipment'])->name('suppliers.edit-shipment');
        Route::delete('suppliers/settings/shipment/{shipmentid}', [StoreOwnerSupplierSettingController::class, 'deleteShipment'])->name('suppliers.delete-shipment');
        
        // Purchase Payment Methods
        Route::post('suppliers/settings/update_payment_method', [StoreOwnerSupplierSettingController::class, 'updatePaymentMethod'])->name('suppliers.update-payment-method');
        Route::get('suppliers/settings/payment_method/{purchasepaymentmethodid}/edit', [StoreOwnerSupplierSettingController::class, 'editPaymentMethod'])->name('suppliers.edit-payment-method');
        Route::delete('suppliers/settings/payment_method/{purchasepaymentmethodid}', [StoreOwnerSupplierSettingController::class, 'deletePaymentMethod'])->name('suppliers.delete-payment-method');
        
        // Purchase Measures
        Route::post('suppliers/settings/update_measure', [StoreOwnerSupplierSettingController::class, 'updateMeasure'])->name('suppliers.update-measure');
        Route::get('suppliers/settings/measure/{purchasemeasuresid}/edit', [StoreOwnerSupplierSettingController::class, 'editMeasure'])->name('suppliers.edit-measure');
        Route::delete('suppliers/settings/measure/{purchasemeasuresid}', [StoreOwnerSupplierSettingController::class, 'deleteMeasure'])->name('suppliers.delete-measure');
        
        // Tax Settings
        Route::post('suppliers/settings/update_tax', [StoreOwnerSupplierSettingController::class, 'updateTax'])->name('suppliers.update-tax');
        Route::get('suppliers/settings/tax/{taxid}/edit', [StoreOwnerSupplierSettingController::class, 'editTax'])->name('suppliers.edit-tax');
        Route::delete('suppliers/settings/tax/{taxid}', [StoreOwnerSupplierSettingController::class, 'deleteTax'])->name('suppliers.delete-tax');

        Route::put('password', [StoreOwnerPasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [StoreOwnerAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('profile', [StoreOwnerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [StoreOwnerProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [StoreOwnerProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

