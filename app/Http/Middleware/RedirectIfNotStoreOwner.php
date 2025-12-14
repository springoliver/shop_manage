<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotStoreOwner
{
    /**
     * Handle an incoming request.
     * Allows storeowners OR employees with Admin/View level module access (like CI).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow storeowners (they have full access)
        if (Auth::guard('storeowner')->check()) {
            return $next($request);
        }
        
        // Check if employee is authenticated
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $storeid = session('storeid', $employee->storeid ?? 0);
            
            if (!$storeid) {
                return redirect()->route('employee.login');
            }
            
            // Check if employee's usergroup has Admin or View access to any module
            // This allows employees with proper access levels to access storeowner routes (like CI)
            $hasAccess = DB::table('stoma_module_access as ma')
                ->join('stoma_paid_module as pm', function($join) use ($storeid) {
                    $join->on('pm.moduleid', '=', 'ma.moduleid')
                         ->where('pm.storeid', '=', $storeid)
                         ->whereDate('pm.purchase_date', '<=', DB::raw('CURDATE()'))
                         ->whereDate('pm.expire_date', '>=', DB::raw('CURDATE()'))
                         ->where('pm.status', '=', 'Enable');
                })
                ->where('ma.storeid', $storeid)
                ->where('ma.usergroupid', $employee->usergroupid)
                ->whereIn('ma.level', ['Admin', 'View'])
                ->exists();
            
            if ($hasAccess) {
                // Employee has Admin/View access to at least one module, allow access (like CI)
                return $next($request);
            } else {
                // Employee doesn't have proper access, redirect to employee login
                return redirect()->route('employee.login')
                    ->with('error', 'You do not have permission to access this page.');
            }
        }
        
        // Not authenticated as either storeowner or employee, redirect to storeowner login
        return redirect()->route('storeowner.login');
    }
}

