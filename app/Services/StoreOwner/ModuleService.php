<?php

namespace App\Services\StoreOwner;

use App\Models\PaidModule;
use App\Models\ModuleAccess;
use Carbon\Carbon;

class ModuleService
{
    /**
     * Get installed modules for a store (active, not expired).
     * Similar to CI's get_installed_modules method.
     *
     * @param int $storeid
     * @param int|null $usergroupid Optional usergroupid to get access level
     * @param int|null $employeeid Optional employeeid to get access level based on employee's usergroup
     * @return array
     */
    public function getInstalledModules(int $storeid, ?int $usergroupid = null, ?int $employeeid = null): array
    {
        $curDate = Carbon::now()->format('Y-m-d');
        
        // If employeeid is provided, get their usergroupid
        if ($employeeid !== null && $usergroupid === null) {
            $employee = \App\Models\Employee::find($employeeid);
            $usergroupid = $employee ? $employee->usergroupid : null;
        }
        
        $installedModules = PaidModule::with('module')
            ->where('storeid', $storeid)
            ->whereDate('purchase_date', '<=', $curDate)
            ->whereDate('expire_date', '>=', $curDate)
            ->where('status', 'Enable')
            ->get()
            ->map(function($pm) use ($storeid, $usergroupid) {
                $moduleData = [
                    'moduleid' => $pm->moduleid,
                    'module' => $pm->module->module ?? '',
                ];
                
                // If usergroupid is provided, get the access level
                if ($usergroupid !== null) {
                    $moduleAccess = ModuleAccess::where('storeid', $storeid)
                        ->where('usergroupid', $usergroupid)
                        ->where('moduleid', $pm->moduleid)
                        ->first();
                    
                    $moduleData['level'] = $moduleAccess ? $moduleAccess->level : 'None';
                }
                
                return $moduleData;
            })
            ->toArray();
        
        return $installedModules;
    }
    
    /**
     * Get installed modules with access levels for an employee.
     * This matches CI's get_installed_modules for employees.
     *
     * @param int $storeid
     * @param int $employeeid
     * @return array
     */
    public function getInstalledModulesForEmployee(int $storeid, int $employeeid): array
    {
        $employee = \App\Models\Employee::find($employeeid);
        if (!$employee) {
            return [];
        }
        
        return $this->getInstalledModules($storeid, $employee->usergroupid, null);
    }
    
    /**
     * Check if a specific module is installed.
     *
     * @param int $storeid
     * @param string $moduleName
     * @return bool
     */
    public function isModuleInstalled(int $storeid, string $moduleName): bool
    {
        $installedModules = $this->getInstalledModules($storeid);
        
        foreach ($installedModules as $module) {
            if ($module['module'] === $moduleName) {
                return true;
            }
        }
        
        return false;
    }
}

