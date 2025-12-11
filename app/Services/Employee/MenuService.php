<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\StoreOwner\ModuleService;

class MenuService
{
    protected ModuleService $moduleService;
    protected int $storeid;
    
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $employee = Auth::guard('employee')->user();
        $this->storeid = $employee ? $employee->storeid : 0;
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
            [
                'label' => 'My Roster',
                'route' => 'employee.roster.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-th"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'Time of request',
                'route' => 'employee.holidayrequest.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-plane"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'Resignation',
                'route' => 'employee.resignation.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-file"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'My Payroll',
                'route' => 'employee.payroll.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-usd"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'My Documents',
                'route' => 'employee.document.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-file"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'POS (Point Of Sale)',
                'route' => 'employee.pos.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-bank"></i>',
                'type' => 'link',
            ],
            [
                'label' => 'Suggest a new module',
                'route' => 'employee.requestmodule.index',
                'enabled' => true,
                'icon' => '<i class="fa fa-cog"></i>',
                'type' => 'link',
            ]
        ];
        
        return array_filter($menu);
    }
}

