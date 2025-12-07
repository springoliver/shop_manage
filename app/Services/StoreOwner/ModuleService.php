<?php

namespace App\Services\StoreOwner;

use App\Models\PaidModule;
use Carbon\Carbon;

class ModuleService
{
    /**
     * Get installed modules for a store (active, not expired).
     * Similar to CI's get_installed_modules method.
     *
     * @param int $storeid
     * @return array
     */
    public function getInstalledModules(int $storeid): array
    {
        $curDate = Carbon::now()->format('Y-m-d');
        
        $installedModules = PaidModule::with('module')
            ->where('storeid', $storeid)
            ->whereDate('purchase_date', '<=', $curDate)
            ->whereDate('expire_date', '>=', $curDate)
            ->where('status', 'Enable')
            ->get()
            ->map(function($pm) {
                return [
                    'moduleid' => $pm->moduleid,
                    'module' => $pm->module->module ?? '',
                ];
            })
            ->toArray();
        
        return $installedModules;
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

