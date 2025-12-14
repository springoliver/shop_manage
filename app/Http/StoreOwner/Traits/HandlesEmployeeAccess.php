<?php

namespace App\Http\StoreOwner\Traits;

use Illuminate\Support\Facades\Auth;

trait HandlesEmployeeAccess
{
    /**
     * Get storeid from either storeowner or employee guard.
     * This allows employees with proper module access to use storeowner routes.
     * 
     * @return int
     */
    protected function getStoreId(): int
    {
        // Check storeowner guard first
        $storeowner = Auth::guard('storeowner')->user();
        if ($storeowner) {
            return session('storeid', $storeowner->stores->first()->storeid ?? 0);
        }
        
        // Check employee guard
        $employee = Auth::guard('employee')->user();
        if ($employee) {
            return session('storeid', $employee->storeid ?? 0);
        }
        
        return 0;
    }
}

