<?php

use App\Http\Controllers\WebServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Service API Routes (for Android App)
|--------------------------------------------------------------------------
|
| These routes match the CI/ws/webservice endpoints for Android app compatibility
| Base URL: /ws/webservice/
|
*/

Route::prefix('ws/webservice')->group(function () {

    // Handle CORS preflight requests
    Route::options('{any}', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    })->where('any', '.*');

    // Store Login endpoint
    Route::post('Store_Login', [WebServiceController::class, 'storeLogin'])->name('webservice.store-login');
    
    // Get clock details endpoint
    Route::post('get_clockdetails', [WebServiceController::class, 'getClockDetails'])->name('webservice.get-clockdetails');
    
    // Insert clock details endpoint
    Route::post('insert_clockdetails', [WebServiceController::class, 'insertClockDetails'])->name('webservice.insert-clockdetails');
    
    // Check clock in-out module endpoint
    Route::post('check_clock_in_out_module', [WebServiceController::class, 'checkClockInOutModule'])->name('webservice.check-clock-module');
    
    // Break management endpoints
    Route::post('break_start', [WebServiceController::class, 'breakStart'])->name('webservice.break-start');
    Route::post('break_end', [WebServiceController::class, 'breakEnd'])->name('webservice.break-end');
    Route::post('get_break_status', [WebServiceController::class, 'getBreakStatus'])->name('webservice.get-break-status');
});

