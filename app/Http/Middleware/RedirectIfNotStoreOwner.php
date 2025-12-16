<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\StoreOwner\PermissionService;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotStoreOwner
{
    protected PermissionService $permissionService;
    
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    
    /**
     * Handle an incoming request.
     * Allows storeowners OR employees with Admin/View level module access (like CI).
     * For employees, checks URL-specific permissions based on their user group.
     * Storeowner-only routes (store, usergroup, department, modulesetting) are blocked for employees.
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
                return redirect()->route('storeowner.login')
                    ->with('error', 'Store not found. Please login again.');
            }
            
            // Get current URL to check if it's a storeowner-only route
            $currentUrl = $this->getCurrentUrl($request);
            
            // Check if this is a storeowner-only route (employees should never access these)
            if ($this->isStoreOwnerOnlyRoute($currentUrl)) {
                // Employee trying to access storeowner-only route, redirect to login (like CI)
                return redirect()->route('storeowner.login')
                    ->with('error', 'You are unauthorized to access this page.');
            }
            
            // First check if employee's usergroup has Admin or View access to any module
            // This allows employees with proper access levels to access storeowner routes (like CI)
            $hasAnyAccess = DB::table('stoma_module_access as ma')
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
            
            if (!$hasAnyAccess) {
                // Employee doesn't have access to any module, redirect to storeowner login (like CI)
                return redirect()->route('storeowner.login')
                    ->with('error', 'You are unauthorized to access this page.');
            }
            
            // Check URL-specific permission (like CI does)
            $hasUrlPermission = $this->permissionService->hasUrlPermission(
                $storeid,
                $employee->usergroupid,
                $currentUrl
            );
            
            if (!$hasUrlPermission) {
                // Employee doesn't have permission for this specific URL, redirect to storeowner login (like CI)
                return redirect()->route('storeowner.login')
                    ->with('error', 'You are unauthorized to access this page.');
            }
            
            // Employee has permission, allow access
            return $next($request);
        }
        
        // Not authenticated as either storeowner or employee, redirect to storeowner login
        return redirect()->route('storeowner.login');
    }
    
    /**
     * Check if a route is storeowner-only (employees should never access these).
     * Based on CI behavior, these routes require storeowner authentication.
     * 
     * @param string $url
     * @return bool
     */
    protected function isStoreOwnerOnlyRoute(string $url): bool
    {
        $storeOwnerOnlyRoutes = [
            'store',
            'usergroup',
            'department',
            'modulesetting',
            'dashboard', // Dashboard is storeowner-only in CI
        ];
        
        // Check if URL starts with any storeowner-only route
        foreach ($storeOwnerOnlyRoutes as $route) {
            if ($url === $route || strpos($url, $route . '/') === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get current URL from request (matching CI's format).
     * Maps Laravel routes to CI route format for permission checking.
     * 
     * @param Request $request
     * @return string
     */
    protected function getCurrentUrl(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();
        
        // Remove 'storeowner' prefix if present (Laravel route prefix)
        $path = preg_replace('/^storeowner\//', '', $path);
        
        // Remove route parameters first (e.g., 'employee/123/edit' -> 'employee/edit')
        $path = preg_replace('/\/[^\/]+\/(edit|view|change-status|change_status)$/', '/$1', $path);
        $path = preg_replace('/\/[^\/]+$/', '', $path); // Remove last segment if it's an ID
        
        // Map Laravel route patterns to CI format based on path and method
        $pathLower = strtolower($path);
        
        // Map common Laravel routes to CI format
        if ($pathLower === 'employee/create' || $pathLower === 'employee/add') {
            return 'employee/add';
        }
        
        if (preg_match('/^employee\/\d+\/edit$/', $pathLower) || $pathLower === 'employee/edit') {
            return 'employee/edit';
        }
        
        if ($method === 'PUT' && preg_match('/^employee\/\d+$/', $pathLower)) {
            return 'employee/update';
        }
        
        if ($method === 'DELETE' && preg_match('/^employee\/\d+$/', $pathLower)) {
            return 'employee/delete';
        }
        
        if (preg_match('/^employee\/\d+$/', $pathLower) || $pathLower === 'employee/view') {
            return 'employee/view';
        }
        
        if ($pathLower === 'employee') {
            return 'employee';
        }
        
        // Roster mappings
        if ($pathLower === 'roster/create' || $pathLower === 'roster/addroster') {
            return 'roster/addroster';
        }
        
        if ($pathLower === 'roster/week' || $pathLower === 'roster/weekroster') {
            return 'roster/weekroster';
        }
        
        if ($pathLower === 'roster/week/add' || $pathLower === 'roster/weekrosteradd') {
            return 'roster/weekrosteradd';
        }
        
        if (preg_match('/^roster\/.*view/', $pathLower) || $pathLower === 'roster/view') {
            return 'roster/view';
        }
        
        if ($pathLower === 'roster') {
            return 'roster';
        }
        
        // Holiday Request mappings
        if (preg_match('/^holidayrequest\/\d+$/', $pathLower) || $pathLower === 'holidayrequest/view') {
            return 'holidayrequest/view';
        }
        
        if (preg_match('/holidayrequest.*change.status/', $pathLower) || $pathLower === 'holidayrequest/change_status') {
            return 'holidayrequest/change_status';
        }
        
        if ($pathLower === 'holidayrequest') {
            return 'holidayrequest';
        }
        
        // Document mappings
        if ($pathLower === 'document/create' || $pathLower === 'document/add') {
            return 'document/add';
        }
        
        if (preg_match('/^document\/\d+$/', $pathLower) && $method === 'PUT') {
            return 'document/update';
        }
        
        if (preg_match('/^document\/\d+$/', $pathLower) && $method === 'DELETE') {
            return 'document/delete';
        }
        
        if ($pathLower === 'document') {
            return 'document';
        }
        
        // Resignation mappings
        if (preg_match('/^resignation\/\d+$/', $pathLower) || $pathLower === 'resignation/view') {
            return 'resignation/view';
        }
        
        if (preg_match('/resignation.*change.status/', $pathLower) || $pathLower === 'resignation/change_status') {
            return 'resignation/change_status';
        }
        
        if ($pathLower === 'resignation') {
            return 'resignation';
        }
        
        // Employee Payroll mappings
        if (preg_match('/^employeepayroll\/\d+$/', $pathLower) || $pathLower === 'employeepayroll/view') {
            return 'employeepayroll/view';
        }
        
        if ($pathLower === 'employeepayroll') {
            return 'employeepayroll';
        }
        
        // Employee Reviews mappings
        if ($pathLower === 'employeereviews/create' || $pathLower === 'employeereviews/add_review') {
            return 'employeereviews/add_review';
        }
        
        if (preg_match('/^employeereviews\/\d+\/edit$/', $pathLower) || $pathLower === 'employeereviews/edit_review') {
            return 'employeereviews/edit_review';
        }
        
        if ($pathLower === 'employeereviews') {
            return 'employeereviews';
        }
        
        // Clock Time mappings
        if ($pathLower === 'clocktime/clockreport') {
            return 'clocktime/clockreport';
        }
        
        if ($pathLower === 'clocktime') {
            return 'clocktime';
        }
        
        // Remove trailing slashes
        $path = rtrim($path, '/');
        
        // Convert to lowercase to match CI's comparison
        return strtolower($path);
    }
}

