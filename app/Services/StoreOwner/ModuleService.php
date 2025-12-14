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
     * This matches CI's get_installed_modules for employees exactly.
     * Uses RIGHT JOINs to ONLY return modules that have module_access entries for the employee's usergroup.
     * If a module has no module_access entry for the employee's usergroup, it won't appear in results.
     *
     * @param int $storeid
     * @param int $employeeid
     * @return array
     */
    public function getInstalledModulesForEmployee(int $storeid, int $employeeid): array
    {
        // Get employee fresh from database to ensure we have latest usergroupid (bypass any caching)
        $employee = \App\Models\Employee::withoutGlobalScopes()->find($employeeid);
        if (!$employee) {
            return [];
        }
        
        // Refresh the model to ensure we have the latest data
        $employee->refresh();
        $usergroupid = $employee->usergroupid;
        
        if (!$usergroupid) {
            return [];
        }
        
        // Verify employee belongs to the correct store
        if ($employee->storeid != $storeid) {
            return [];
        }
        
        $curDate = Carbon::now()->format('Y-m-d');
        
        // Match CI's query exactly: RIGHT JOIN on module_access and employee
        // This ensures we ONLY get modules that have entries in module_access for this employee's usergroup
        // CI query structure: FROM paid_module pm LEFT JOIN module m RIGHT JOIN module_access ma RIGHT JOIN employee e
        // The RIGHT JOIN on employee ensures we only get modules for this specific employee's usergroup
        // Filtering by e.employeeid ensures we get the employee's usergroupid, which then matches ma.usergroupid
        $installedModules = \Illuminate\Support\Facades\DB::select("
            SELECT 
                pm.moduleid,
                m.moduleid as m_moduleid,
                m.module, 
                m.price_1months, 
                m.module_description, 
                ma.level
            FROM stoma_paid_module pm
            LEFT JOIN stoma_module m ON m.moduleid = pm.moduleid
            RIGHT JOIN stoma_module_access ma ON ma.moduleid = pm.moduleid AND ma.storeid = pm.storeid
            RIGHT JOIN stoma_employee e ON e.usergroupid = ma.usergroupid
            WHERE DATE(pm.purchase_date) <= ?
            AND DATE(pm.expire_date) >= ?
            AND pm.storeid = ?
            AND e.employeeid = ?
            GROUP BY ma.moduleid, pm.moduleid, m.moduleid, m.module, m.price_1months, m.module_description, ma.level
        ", [$curDate, $curDate, $storeid, $employeeid]);
        
        $result = array_map(function($row) {
            $rowArray = (array) $row;
            return [
                'moduleid' => $rowArray['moduleid'] ?? $rowArray['m_moduleid'] ?? null,
                'module' => $rowArray['module'] ?? '',
                'level' => $rowArray['level'] ?? 'None',
                'price_1months' => $rowArray['price_1months'] ?? null,
                'module_description' => $rowArray['module_description'] ?? null,
            ];
        }, $installedModules);
        
        // Filter out any modules without a module name (shouldn't happen, but just in case)
        return array_values(array_filter($result, function($module) {
            return !empty($module['module']);
        }));
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

