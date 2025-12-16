<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\StoreOwner\ModuleService;
use App\Services\StoreOwner\PermissionService;

class MenuService
{
    protected ModuleService $moduleService;
    protected PermissionService $permissionService;
    protected int $storeid;
    protected int $employeeid;
    protected int $usergroupid;
    
    public function __construct(ModuleService $moduleService, PermissionService $permissionService)
    {
        $this->moduleService = $moduleService;
        $this->permissionService = $permissionService;
        $employee = Auth::guard('employee')->user();
        $this->storeid = $employee ? $employee->storeid : 0;
        $this->employeeid = $employee ? $employee->employeeid : 0;
        $this->usergroupid = $employee ? $employee->usergroupid : 0;
    }
    
    /**
     * Build the menu structure based on installed modules and access levels.
     *
     * @return array
     */
    public function buildMenu(): array
    {
        // Get installed modules with access levels for this employee (for admin/management items)
        $installedModules = $this->moduleService->getInstalledModulesForEmployee($this->storeid, $this->employeeid);
        
        // Also get all installed modules (regardless of access level) for personal menu items
        // Personal menu items (My Roster, My Payroll, etc.) should always show if module is installed
        $allInstalledModules = $this->moduleService->getInstalledModules($this->storeid);
        
        // Create a map of module name to access level for quick lookup (for admin/management items)
        $moduleAccessMap = [];
        foreach ($installedModules as $module) {
            $moduleAccessMap[$module['module']] = $module['level'] ?? 'None';
        }
        
        // Create a map of all installed modules (for personal menu items)
        $allInstalledModulesMap = [];
        foreach ($allInstalledModules as $module) {
            $allInstalledModulesMap[$module['module']] = true;
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
        
        // My Roster - shown if Roster module is installed (always visible, no level check)
        if (isset($allInstalledModulesMap['Roster'])) {
            $menu[] = [
                'label' => 'My Roster',
                'route' => 'employee.roster.index',
                'enabled' => Route::has('employee.roster.index'),
                'icon' => '<i class="fa fa-th"></i>',
                'type' => 'link',
            ];
        }

         
        // Time of request - shown if Time Off Request module is installed (always visible, no level check)
        if (isset($allInstalledModulesMap['Time Off Request'])) {
            $menu[] = [
                'label' => 'Time of request',
                'route' => 'employee.holidayrequest.index',
                'enabled' => Route::has('employee.holidayrequest.index'),
                'icon' => '<i class="fa fa-plane"></i>',
                'type' => 'link',
            ];
        }

         
        // Resignation - shown if Resignation module is installed (always visible, no level check)
        if (isset($allInstalledModulesMap['Resignation'])) {
            $menu[] = [
                'label' => 'Resignation',
                'route' => 'employee.resignation.index',
                'enabled' => Route::has('employee.resignation.index'),
                'icon' => '<i class="fa fa-sign-out"></i>',
                'type' => 'link',
            ];
        }
        
        // My Payroll - shown if Clock in-out module is installed (always visible, no level check)
        if (isset($allInstalledModulesMap['Clock in-out'])) {
            $menu[] = [
                'label' => 'My Payroll',
                'route' => 'employee.payroll.index',
                'enabled' => Route::has('employee.payroll.index'),
                'icon' => '<i class="fa fa-usd"></i>',
                'type' => 'link',
            ];
        }
        
        // My Documents - shown if Employee Documents module is installed (always visible, no level check)
        if (isset($allInstalledModulesMap['Employee Documents'])) {
            $menu[] = [
                'label' => 'My Documents',
                'route' => 'employee.document.index',
                'enabled' => Route::has('employee.document.index'),
                'icon' => '<i class="fa fa-file"></i>',
                'type' => 'link',
            ];
        }
        
        // Store Documents submenu - check module access AND URL permission
        if (isset($moduleAccessMap['Store Documents']) && $moduleAccessMap['Store Documents'] != 'None') {
            $storeDocsSubmenu = [];
            
            if (Route::has('storeowner.storedocument.index')) {
                if ($this->hasUrlPermission('storedocument')) {
                    $storeDocsSubmenu[] = [
                        'label' => 'Documents',
                        'route' => 'storeowner.storedocument.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
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
        
        // Based on store owner sidebar, POS has submenu, so check module access AND URL permission
        if (isset($moduleAccessMap['Point Of Sale']) && $moduleAccessMap['Point Of Sale'] != 'None') {
            if ($this->hasUrlPermission('pos')) {
                $menu[] = [
                    'label' => 'POS (Point Of Sale)',
                    'route' => 'employee.pos.index',
                    'enabled' => Route::has('employee.pos.index'),
                    'icon' => '<i class="fa fa-bank"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Employee Management submenu - check both module access AND URL permissions
        $employeeMgmtSubmenu = [];
        
        // Employees - check URL permission for 'employee' route
        if (Route::has('storeowner.employee.index')) {
            if ($this->hasUrlPermission('employee')) {
                $employeeMgmtSubmenu[] = [
                    'label' => 'Employee',
                    'route' => 'storeowner.employee.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-user"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Employee Payroll - check module access AND URL permission
        if (isset($moduleAccessMap['Employee Payroll']) && $moduleAccessMap['Employee Payroll'] != 'None') {
            if (Route::has('storeowner.employeepayroll.index')) {
                if ($this->hasUrlPermission('employeepayroll')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Employee Payroll',
                        'route' => 'storeowner.employeepayroll.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-users"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Roster - check module access AND URL permission
        if (isset($moduleAccessMap['Roster']) && $moduleAccessMap['Roster'] != 'None') {
            if (Route::has('storeowner.roster.index')) {
                if ($this->hasUrlPermission('roster')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Employee Roster',
                        'route' => 'storeowner.roster.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-users"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Clock in-out - check module access AND URL permission
        if (isset($moduleAccessMap['Clock in-out']) && $moduleAccessMap['Clock in-out'] != 'None') {
            if (Route::has('storeowner.clocktime.index')) {
                if ($this->hasUrlPermission('clocktime')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Clock in-out',
                        'route' => 'storeowner.clocktime.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-clock"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Time Off Request - check module access AND URL permission
        if (isset($moduleAccessMap['Time Off Request']) && $moduleAccessMap['Time Off Request'] != 'None') {
            if (Route::has('storeowner.holidayrequest.index')) {
                if ($this->hasUrlPermission('holidayrequest')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Time Off Request',
                        'route' => 'storeowner.holidayrequest.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-calendar"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Employee Documents - check module access AND URL permission
        if (isset($moduleAccessMap['Employee Documents']) && $moduleAccessMap['Employee Documents'] != 'None') {
            if (Route::has('storeowner.document.index')) {
                if ($this->hasUrlPermission('document')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Employee Documents',
                        'route' => 'storeowner.document.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Resignation - check module access AND URL permission
        if (isset($moduleAccessMap['Resignation']) && $moduleAccessMap['Resignation'] != 'None') {
            if (Route::has('storeowner.resignation.index')) {
                if ($this->hasUrlPermission('resignation')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Resignation',
                        'route' => 'storeowner.resignation.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        // Employee Reviews - check module access AND URL permission
        if (isset($moduleAccessMap['Employee Reviews']) && $moduleAccessMap['Employee Reviews'] != 'None') {
            if (Route::has('storeowner.employeereviews.index')) {
                if ($this->hasUrlPermission('employeereviews')) {
                    $employeeMgmtSubmenu[] = [
                        'label' => 'Employee Reviews',
                        'route' => 'storeowner.employeereviews.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
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
        
        // Daily Management submenu - check module access AND URL permission
        if (isset($moduleAccessMap['Daily Report']) && $moduleAccessMap['Daily Report'] != 'None') {
            $dailyMgmtSubmenu = [];
            
            if (Route::has('storeowner.dailyreport.index')) {
                if ($this->hasUrlPermission('dailyreport')) {
                    $dailyMgmtSubmenu[] = [
                        'label' => 'Daily Report',
                        'route' => 'storeowner.dailyreport.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
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
        
        // Suppliers submenu - check module access AND URL permission
        $suppliersSubmenu = [];
        $showSuppliersMenu = false;
        
        if (isset($moduleAccessMap['Suppliers']) && $moduleAccessMap['Suppliers'] != 'None') {
            if (Route::has('storeowner.suppliers.index')) {
                if ($this->hasUrlPermission('suppliers')) {
                    $showSuppliersMenu = true;
                    $suppliersSubmenu[] = [
                        'label' => 'Suppliers',
                        'route' => 'storeowner.suppliers.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
            }
        }
        
        if (isset($moduleAccessMap['Products']) && $moduleAccessMap['Products'] != 'None') {
            if (Route::has('storeowner.products.index')) {
                if ($this->hasUrlPermission('products')) {
                    $showSuppliersMenu = true;
                    $suppliersSubmenu[] = [
                        'label' => 'Products',
                        'route' => 'storeowner.products.index',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
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
        
        // Purchase Order submenu - check module access AND URL permission
        if (isset($moduleAccessMap['Ordering']) && $moduleAccessMap['Ordering'] != 'None') {
            $poSubmenu = [];
            
            if (Route::has('storeowner.ordering.order')) {
                if ($this->hasUrlPermission('ordering/order')) {
                    $poSubmenu[] = [
                        'label' => 'New Purchase Order',
                        'route' => 'storeowner.ordering.order',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-file-text"></i>',
                        'type' => 'link',
                    ];
                }
            }
            
            if (Route::has('storeowner.ordering.report')) {
                if ($this->hasUrlPermission('ordering/report')) {
                    $poSubmenu[] = [
                        'label' => 'PO Reports',
                        'route' => 'storeowner.ordering.report',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-bar-chart"></i>',
                        'type' => 'link',
                    ];
                }
            }
            
            if (Route::has('storeowner.ordering.product-report')) {
                if ($this->hasUrlPermission('ordering/product-report')) {
                    $poSubmenu[] = [
                        'label' => 'Ordered Product Reports',
                        'route' => 'storeowner.ordering.product-report',
                        'enabled' => true,
                        'icon' => '<i class="fa fa-bar-chart"></i>',
                        'type' => 'link',
                    ];
                }
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
    
    /**
     * Check if employee has URL permission for a given route.
     * 
     * @param string $url The CI-like URL (e.g., 'employee', 'roster', 'holidayrequest')
     * @return bool
     */
    protected function hasUrlPermission(string $url): bool
    {
        if (!$this->storeid || !$this->usergroupid) {
            return false;
        }
        
        return $this->permissionService->hasUrlPermission($this->storeid, $this->usergroupid, $url);
    }
}
