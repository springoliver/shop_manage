<?php

namespace App\Services\StoreOwner;

use Illuminate\Support\Facades\Route;

class MenuService
{
    protected ModuleService $moduleService;
    protected int $storeid;
    
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $this->storeid = session('storeid', 0);
    }
    
    /**
     * Build the menu structure based on installed modules.
     *
     * @return array
     */
    public function buildMenu(): array
    {
        $installedModules = $this->moduleService->getInstalledModules($this->storeid);
        $installedModuleNames = array_column($installedModules, 'module');
        
        $menu = [
            [
                'label' => 'Dashboard',
                'route' => 'storeowner.dashboard',
                'enabled' => true,
                'icon' => '<i class="fa fa-dashboard"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'My Stores',
                'route' => 'storeowner.store.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-university"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'User Groups',
                'route' => 'storeowner.usergroup.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-users"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'Departments',
                'route' => 'storeowner.department.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-th"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'Modules',
                'route' => 'storeowner.modulesetting.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-wrench"></i>',
                'type' => 'link',
            ],
            // Employee Management submenu
            $this->buildEmployeeManagementMenu($installedModuleNames),
            // Dynamic module-based menus
            $this->buildPointOfSaleMenu($installedModuleNames),
            $this->buildSuppliersMenu($installedModuleNames),
            $this->buildPurchaseOrderMenu($installedModuleNames),
        ];
        
        // Filter out null items (modules not installed)
        return array_filter($menu);
    }
    
    /**
     * Build Employee Management submenu.
     *
     * @param array $installedModuleNames
     * @return array
     */
    protected function buildEmployeeManagementMenu(array $installedModuleNames): array
    {
        $submenu = [];
        
        // Employees - always shown (but check if route exists)
        if (Route::has('storeowner.employee.index')) {
            $submenu[] = [
                'label' => 'Employees',
                'route' => 'storeowner.employee.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-user"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Payroll
        if (in_array('Employee Payroll', $installedModuleNames) && Route::has('storeowner.employeepayroll.index')) {
            $submenu[] = [
                'label' => 'Employee Payroll',
                'route' => 'storeowner.employeepayroll.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-user"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Roster
        if (in_array('Roster', $installedModuleNames) && Route::has('storeowner.roster.index')) {
            $submenu[] = [
                'label' => 'Employee Roster',
                'route' => 'storeowner.roster.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-calendar"></i>',
                'type' => 'link',
            ];
        }
        
        // Clock in-out
        if (in_array('Clock in-out', $installedModuleNames)) {
            if (Route::has('storeowner.clocktime.index')) {
                $submenu[] = [
                    'label' => 'Clock in-out',
                    'route' => 'storeowner.clocktime.index',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-clock"></i>',
                    'type' => 'link',
                ];
            }
            
            // Employee Holidays (part of Clock in-out module)
            if (Route::has('storeowner.clocktime.employee_holidays')) {
                $submenu[] = [
                    'label' => 'Employee Holidays',
                    'route' => 'storeowner.clocktime.employee_holidays',
                    'enabled' => true,
                    'icon' => '<i class="fa fa-clock"></i>',
                    'type' => 'link',
                ];
            }
        }
        
        // Employee Documents
        if (in_array('Employee Documents', $installedModuleNames) && Route::has('storeowner.document.index')) {
            $submenu[] = [
                'label' => 'Employee Documents',
                'route' => 'storeowner.document.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-file"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Resignation
        if (in_array('Resignation', $installedModuleNames) && Route::has('storeowner.resignation.index')) {
            $submenu[] = [
                'label' => 'Employee Resignation',
                'route' => 'storeowner.resignation.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-user"></i>',
                'type' => 'link',
            ];
        }
        
        // Time Off Request
        if (in_array('Time Off Request', $installedModuleNames) && Route::has('storeowner.holidayrequest.index')) {
            $submenu[] = [
                'label' => 'Time Off Request',
                'route' => 'storeowner.holidayrequest.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-calendar"></i>',
                'type' => 'link',
            ];
        }
        
        // Employee Reviews - show if route exists (controller handles module check)
        if (Route::has('storeowner.employeereviews.index')) {
            $submenu[] = [
                'label' => 'Employee Reviews',
                'route' => 'storeowner.employeereviews.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-file-text"></i>',
                'type' => 'link',
            ];
        }
        
        // Always return Employee Management menu (even if empty, it will be shown but collapsed)
        // This matches CI behavior where Employee Management is always visible
        return [
            'label' => 'Employee Management',
            'enabled' => true,
            'icon' => '<i class="fa fa-users"></i>',
            'type' => 'submenu',
            'submenu' => $submenu,
        ];
    }
    
    /**
     * Build Point Of Sale menu (if module installed).
     *
     * @param array $installedModuleNames
     * @return array|null
     */
    protected function buildPointOfSaleMenu(array $installedModuleNames): ?array
    {
        if (!in_array('Point Of Sale', $installedModuleNames)) {
            return null;
        }
        
        $submenu = [];
        
        if (Route::has('storeowner.possetting.sections')) {
            $submenu[] = [
                'label' => 'Settings',
                'route' => 'storeowner.possetting.sections',
                'enabled' => true,
                'icon' => '<i class="glyphicon glyphicon-cog"></i>',
                'type' => 'link',
            ];
        }
        
        if (count($submenu) === 0) {
            return null;
        }
        
        return [
            'label' => 'Point Of Sale',
            'enabled' => true,
            'icon' => '<i class="fa fa-suitcase"></i>',
            'type' => 'submenu',
            'submenu' => $submenu,
        ];
    }
    
    /**
     * Build Suppliers menu (if module installed).
     *
     * @param array $installedModuleNames
     * @return array|null
     */
    protected function buildSuppliersMenu(array $installedModuleNames): ?array
    {
        if (!in_array('Suppliers', $installedModuleNames)) {
            return null;
        }
        
        $submenu = [];
        
        if (Route::has('storeowner.suppliers.settings')) {
            $submenu[] = [
                'label' => 'Settings',
                'route' => 'storeowner.suppliers.settings',
                'enabled' => true,
                'icon' => '<i class="glyphicon glyphicon-cog"></i>',
                'type' => 'link',
            ];
        }
        
        if (Route::has('storeowner.suppliers.index')) {
            $submenu[] = [
                'label' => 'Suppliers',
                'route' => 'storeowner.suppliers.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        // Products (if Products module is also installed)
        if (in_array('Products', $installedModuleNames) && Route::has('storeowner.products.index')) {
            $submenu[] = [
                'label' => 'Products',
                'route' => 'storeowner.products.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (count($submenu) === 0) {
            return null;
        }
        
        return [
            'label' => 'Suppliers',
            'enabled' => true,
            'icon' => '<i class="fa fa-suitcase"></i>',
            'type' => 'submenu',
            'submenu' => $submenu,
        ];
    }
    
    /**
     * Build Purchase Order menu (if module installed).
     *
     * @param array $installedModuleNames
     * @return array|null
     */
    protected function buildPurchaseOrderMenu(array $installedModuleNames): ?array
    {
        if (!in_array('Ordering', $installedModuleNames)) {
            return null;
        }
        
        $submenu = [];
        
        if (Route::has('storeowner.ordering.settings')) {
            $submenu[] = [
                'label' => 'Settings',
                'route' => 'storeowner.ordering.settings',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (Route::has('storeowner.ordering.order')) {
            $submenu[] = [
                'label' => 'New Purchase Order',
                'route' => 'storeowner.ordering.order',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (Route::has('storeowner.ordering.report')) {
            $submenu[] = [
                'label' => 'PO Reports',
                'route' => 'storeowner.ordering.report',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (Route::has('storeowner.ordering.tax_analysis')) {
            $submenu[] = [
                'label' => 'Invoices & Tax',
                'route' => 'storeowner.ordering.tax_analysis',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (Route::has('storeowner.ordering.index_supplier_doc')) {
            $submenu[] = [
                'label' => 'Supplier Docs',
                'route' => 'storeowner.ordering.index_supplier_doc',
                'enabled' => true,
                'icon' => '<i class="fa fa-bar-chart"></i>',
                'type' => 'link',
            ];
        }
        
        if (count($submenu) === 0) {
            return null;
        }
        
        return [
            'label' => 'Purchase Order',
            'enabled' => true,
            'icon' => '<i class="fa fa-suitcase"></i>',
            'type' => 'submenu',
            'submenu' => $submenu,
        ];
    }
}

