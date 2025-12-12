<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\StoreOwner\ModuleService;

class MenuService
{
    protected ModuleService $moduleService;
    protected int $storeid;
    protected int $employeeid;
    
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $employee = Auth::guard('employee')->user();
        $this->storeid = $employee ? $employee->storeid : 0;
        $this->employeeid = $employee ? $employee->employeeid : 0;
    }
    
    /**
     * Build the menu structure based on installed modules and access levels.
     *
     * @return array
     */
    public function buildMenu(): array
    {
        // Get installed modules with access levels for this employee
        $installedModules = $this->moduleService->getInstalledModulesForEmployee($this->storeid, $this->employeeid);
        
        // Create a map of module name to access level for quick lookup
        $moduleAccessMap = [];
        foreach ($installedModules as $module) {
            $moduleAccessMap[$module['module']] = $module['level'] ?? 'None';
        }
        
        $menu = [
            [
                'label' => 'Dashboard',
                'route' => 'employee.dashboard',
                'enabled' => true,
                'icon' => '<i class="fa fa-dashboard"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'My Profile',
                'route' => 'employee.profile.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-user"></i>',
                'type' => 'link',
            ],
        ];
        
        // My Roster - shown if Roster module is installed
        if (isset($moduleAccessMap['Roster'])) {
            $menu[] = [
                'label' => 'My Roster',
                'route' => 'employee.roster.index',
                'enabled' => Route::has('employee.roster.index'),
                'icon' => '<i class="fa fa-th"></i>',
                'type' => 'link',
            ];
        }
        
        // My Payroll - shown if Clock in-out module is installed
        if (isset($moduleAccessMap['Clock in-out'])) {
            $menu[] = [
                'label' => 'My Payroll',
                'route' => 'employee.payroll.index',
                'enabled' => Route::has('employee.payroll.index'),
                'icon' => '<i class="fa fa-usd"></i>',
                'type' => 'link',
            ];
        }
        
        // My Documents - shown if Employee Documents module is installed
        if (isset($moduleAccessMap['Employee Documents'])) {
            $menu[] = [
                'label' => 'My Documents',
                'route' => 'employee.document.index',
                'enabled' => Route::has('employee.document.index'),
                'icon' => '<i class="fa fa-file"></i>',
                'type' => 'link',
            ];
        }
        
        // Time of request - shown if Time Off Request module is installed
        if (isset($moduleAccessMap['Time Off Request'])) {
            $menu[] = [
                'label' => 'Time of request',
                'route' => 'employee.holidayrequest.index',
                'enabled' => Route::has('employee.holidayrequest.index'),
                'icon' => '<i class="fa fa-plane"></i>',
                'type' => 'link',
            ];
        }
        
        // Resignation - shown if Resignation module is installed
        if (isset($moduleAccessMap['Resignation'])) {
            $menu[] = [
                'label' => 'Resignation',
                'route' => 'employee.resignation.index',
                'enabled' => Route::has('employee.resignation.index'),
                'icon' => '<i class="fa fa-sign-out"></i>',
                'type' => 'link',
            ];
        }
        
        // Store Documents submenu
        if (isset($moduleAccessMap['Store Documents'])) {
            $storeDocsSubmenu = [];
            
            if (Route::has('storeowner.storedocument.index')) {
                $storeDocsSubmenu[] = [
                    'label' => 'Documents',
                    'route' => 'storeowner.storedocument.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
            
            if (!empty($storeDocsSubmenu)) {
                $menu[] = [
                    'label' => 'Store Documents',
                    'icon' => '<i class="fa fa-suitcase"></i>',
                    'type' => 'submenu',
                    'submenu' => $storeDocsSubmenu,
                ];
            }
        }
        
        // POS (Point Of Sale) - shown if Point Of Sale module is installed
        if (isset($moduleAccessMap['Point Of Sale'])) {
            $menu[] = [
                'label' => 'POS (Point Of Sale)',
                'route' => 'employee.pos.index',
                'enabled' => Route::has('employee.pos.index'),
                'icon' => '<i class="fa fa-bank"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Management submenu - only show items with level != 'None'
        $employeeMgmtSubmenu = [];
        
        // Employees - always shown if route exists
        if (Route::has('storeowner.employee.index')) {
            $employeeMgmtSubmenu[] = [
                'label' => 'Employee',
                'route' => 'storeowner.employee.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-user"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Payroll - check level
        if (isset($moduleAccessMap['Employee Payroll']) && $moduleAccessMap['Employee Payroll'] != 'None') {
            if (Route::has('storeowner.employeepayroll.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Employee Payroll',
                    'route' => 'storeowner.employeepayroll.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-users"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Roster - check level
        if (isset($moduleAccessMap['Roster']) && $moduleAccessMap['Roster'] != 'None') {
            if (Route::has('storeowner.roster.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Roster',
                    'route' => 'storeowner.roster.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-users"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Clock in-out - check level
        if (isset($moduleAccessMap['Clock in-out']) && $moduleAccessMap['Clock in-out'] != 'None') {
            if (Route::has('storeowner.clocktime.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Clock in-out',
                    'route' => 'storeowner.clocktime.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-clock-o"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Time Off Request - check level
        if (isset($moduleAccessMap['Time Off Request']) && $moduleAccessMap['Time Off Request'] != 'None') {
            if (Route::has('storeowner.holidayrequest.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Time Off Request',
                    'route' => 'storeowner.holidayrequest.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-calendar"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Employee Documents - check level
        if (isset($moduleAccessMap['Employee Documents']) && $moduleAccessMap['Employee Documents'] != 'None') {
            if (Route::has('storeowner.document.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Employee Documents',
                    'route' => 'storeowner.document.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Resignation - check level
        if (isset($moduleAccessMap['Resignation']) && $moduleAccessMap['Resignation'] != 'None') {
            if (Route::has('storeowner.resignation.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Resignation',
                    'route' => 'storeowner.resignation.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Employee Reviews - check level
        if (isset($moduleAccessMap['Employee Reviews']) && $moduleAccessMap['Employee Reviews'] != 'None') {
            if (Route::has('storeowner.employeereviews.index')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Employee Reviews',
                    'route' => 'storeowner.employeereviews.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Add Employee Management submenu if it has children
        if (!empty($employeeMgmtSubmenu)) {
            $menu[] = [
                'label' => 'Employee Management',
                'icon' => '<i class="fa fa-users"></i>',
                'type' => 'submenu',
                'submenu' => $employeeMgmtSubmenu,
            ];
        }
        
        // Daily Management submenu - shown if Daily Report module is installed
        if (isset($moduleAccessMap['Daily Report'])) {
            $dailyMgmtSubmenu = [];
            
            if (Route::has('storeowner.dailyreport.index')) {
                $dailyMgmtSubmenu[] = [
                    'label' => 'Daily Report',
                    'route' => 'storeowner.dailyreport.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
            
            if (!empty($dailyMgmtSubmenu)) {
                $menu[] = [
                    'label' => 'Daily Management',
                    'icon' => '<i class="fa fa-suitcase"></i>',
                    'type' => 'submenu',
                    'submenu' => $dailyMgmtSubmenu,
                ];
            }
        }
        
        // Suppliers submenu - shown if Suppliers OR Products module is installed (check level)
        $suppliersSubmenu = [];
        $showSuppliersMenu = false;
        
        if (isset($moduleAccessMap['Suppliers']) && $moduleAccessMap['Suppliers'] != 'None') {
            $showSuppliersMenu = true;
            if (Route::has('storeowner.suppliers.index')) {
                $suppliersSubmenu[] = [
                    'label' => 'Suppliers',
                    'route' => 'storeowner.suppliers.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        if (isset($moduleAccessMap['Products']) && $moduleAccessMap['Products'] != 'None') {
            $showSuppliersMenu = true;
            if (Route::has('storeowner.products.index')) {
                $suppliersSubmenu[] = [
                    'label' => 'Products',
                    'route' => 'storeowner.products.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        if ($showSuppliersMenu && !empty($suppliersSubmenu)) {
            $menu[] = [
                'label' => 'Suppliers',
                'icon' => '<i class="fa fa-suitcase"></i>',
                'type' => 'submenu',
                'submenu' => $suppliersSubmenu,
            ];
        }
        
        // Purchase Order submenu - shown if Ordering module is installed
        if (isset($moduleAccessMap['Ordering'])) {
            $poSubmenu = [];
            
            if (Route::has('storeowner.ordering.order')) {
                $poSubmenu[] = [
                    'label' => 'New Purchase Order',
                    'route' => 'storeowner.ordering.order',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-file-text"></i>',
                    'type' => 'link',
                ];
            }
            
            if (Route::has('storeowner.ordering.report')) {
                $poSubmenu[] = [
                    'label' => 'PO Reports',
                    'route' => 'storeowner.ordering.report',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-bar-chart"></i>',
                    'type' => 'link',
                ];
            }
            
            if (Route::has('storeowner.ordering.product-report')) {
                $poSubmenu[] = [
                    'label' => 'Ordered Product Reports',
                    'route' => 'storeowner.ordering.product-report',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-bar-chart"></i>',
                    'type' => 'link',
                ];
            }
            
            if (!empty($poSubmenu)) {
                $menu[] = [
                    'label' => 'Purchase Order',
                    'icon' => '<i class="fa fa-suitcase"></i>',
                    'type' => 'submenu',
                    'submenu' => $poSubmenu,
                ];
            }
        }
        
        // Suggest a new module - always shown
        $menu[] = [
            'label' => 'Suggest a new module',
            'route' => 'employee.requestmodule.index',
            'enabled' => Route::has('employee.requestmodule.index'),
            'icon' => '<i class="fa fa-cog"></i>',
            'type' => 'link',
        ];
        
        return array_filter($menu);
    }
}
