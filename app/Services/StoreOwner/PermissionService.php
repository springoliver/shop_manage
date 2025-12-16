<?php

namespace App\Services\StoreOwner;

use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Check if employee has permission to access a specific URL/route.
     * 
     * @param int $storeid
     * @param int $usergroupid
     * @param string $url The route URL (e.g., 'employee', 'employee/add', 'roster')
     * @return bool
     */
    public function hasUrlPermission(int $storeid, int $usergroupid, string $url): bool
    {
        // Get module ID from URL
        $moduleid = $this->getModuleIdByUrl($url);
        
        // If URL is not in permission_access table, allow access (not a protected route)
        if (empty($moduleid)) {
            return true;
        }
        
        // Get permission level for this module, usergroup, and store
        $permissionLevel = $this->getPermissionLevel($moduleid, $usergroupid, $storeid);
        
        // If no permission found or level is 'None', deny access
        if (empty($permissionLevel) || $permissionLevel === 'None') {
            return false;
        }
        
        // If Admin level, allow all URLs for this module
        if ($permissionLevel === 'Admin') {
            return true;
        }
        
        // For View level, check if URL is a view-only URL
        if ($permissionLevel === 'View') {
            return $this->isViewOnlyUrl($url);
        }
        
        return false;
    }
    
    /**
     * Check if URL is a view-only URL (allowed for View level access).
     * 
     * @param string $url
     * @return bool
     */
    protected function isViewOnlyUrl(string $url): bool
    {
        // View-only URLs (index, view, show, list pages)
        $viewOnlyPatterns = [
            '/^employee$/',                    // employee index
            '/^employee\/view/',              // employee/view
            '/^employeepayroll/',             // employeepayroll (index)
            '/^employeepayroll\/view/',       // employeepayroll/view
            '/^roster$/',                     // roster index
            '/^roster\/view/',                // roster/view
            '/^roster\/viewweekroster/',      // roster/viewweekroster
            '/^clocktime/',                   // clocktime (index)
            '/^clocktime\/clockreport/',      // clocktime/clockreport
            '/^holidayrequest$/',             // holidayrequest index
            '/^holidayrequest\/view/',        // holidayrequest/view
            '/^document$/',                   // document index
            '/^resignation$/',                // resignation index
            '/^resignation\/view/',           // resignation/view
            '/^employeereviews$/',            // employeereviews index
        ];
        
        foreach ($viewOnlyPatterns as $pattern) {
            if (preg_match($pattern, strtolower($url))) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get module ID from URL using permission_access table.
     * Falls back to route-based mapping if table doesn't exist.
     * 
     * @param string $url
     * @return int|null
     */
    protected function getModuleIdByUrl(string $url): ?int
    {
        // Check if permission_access table exists
        if (!DB::getSchemaBuilder()->hasTable('stoma_permission_access')) {
            // Fallback: Map URLs to module IDs based on route patterns
            return $this->getModuleIdByRoutePattern($url);
        }
        
        $result = DB::table('stoma_permission_access')
            ->where('url', $url)
            ->value('moduleid');
        
        return $result ? (int)$result : null;
    }
    
    /**
     * Fallback method to get module ID from route pattern.
     * Maps common routes to their module IDs based on CI's permission_access data.
     * 
     * @param string $url
     * @return int|null
     */
    protected function getModuleIdByRoutePattern(string $url): ?int
    {
        // Map URLs to module IDs (based on CI's stoma_permission_access table)
        $urlToModuleMap = [
            // Employee Management (module 11)
            'employee' => 11,
            'employee/add' => 11,
            'employee/edit' => 11,
            'employee/update' => 11,
            'employee/view' => 11,
            'employee/delete' => 11,
            'employee/change_status' => 11,
            // Employee Payroll (module 6)
            'employeepayroll' => 6,
            'employeepayroll/view' => 6,
            'employeepayroll/downloadpdf' => 6,
            // Roster (module 1)
            'roster' => 1,
            'roster/addroster' => 1,
            'roster/weekroster' => 1,
            'roster/weekrosteradd' => 1,
            'roster/viewweekroster' => 1,
            'roster/get_roster_data' => 1,
            'roster/delete' => 1,
            'roster/deleteroster' => 1,
            'roster/view' => 1,
            'roster/get_roster_datas' => 1,
            // Clock Time (module 4)
            'clocktime' => 4,
            'clocktime/clockreport' => 4,
            // Holiday Request (module 2)
            'holidayrequest' => 2,
            'holidayrequest/view' => 2,
            'holidayrequest/change_status' => 2,
            // Document (module 5)
            'document' => 5,
            'document/add' => 5,
            'document/update' => 5,
            'document/get_documents' => 5,
            'document/delete' => 5,
            // Resignation (module 3)
            'resignation' => 3,
            'resignation/change_status' => 3,
            'resignation/view' => 3,
            // Employee Reviews (module 26)
            'employeereviews' => 26,
            'employeereviews/add_review' => 26,
            'employeereviews/due_reviews' => 26,
            'employeereviews/edit_review' => 26,
        ];
        
        return $urlToModuleMap[$url] ?? null;
    }
    
    /**
     * Get permission level (Admin/View/None) for module, usergroup, and store.
     * 
     * @param int $moduleid
     * @param int $usergroupid
     * @param int $storeid
     * @return string|null
     */
    protected function getPermissionLevel(int $moduleid, int $usergroupid, int $storeid): ?string
    {
        $result = DB::table('stoma_module_access as ma')
            ->join('stoma_paid_module as pm', function($join) use ($storeid) {
                $join->on('pm.moduleid', '=', 'ma.moduleid')
                     ->where('pm.storeid', '=', $storeid)
                     ->whereDate('pm.purchase_date', '<=', DB::raw('CURDATE()'))
                     ->whereDate('pm.expire_date', '>=', DB::raw('CURDATE()'))
                     ->where('pm.status', '=', 'Enable');
            })
            ->where('ma.storeid', $storeid)
            ->where('ma.usergroupid', $usergroupid)
            ->where('ma.moduleid', $moduleid)
            ->value('ma.level');
        
        return $result;
    }
    
    /**
     * Get allowed URLs for a permission level (Admin/View).
     * 
     * @param string $level
     * @return array
     */
    protected function getAllowedUrlsForLevel(string $level): array
    {
        // Check if permission_groups table exists
        if (!DB::getSchemaBuilder()->hasTable('stoma_permission_groups')) {
            // Fallback: If Admin, allow all URLs; if View, allow view-only URLs
            return $this->getAllowedUrlsByLevelFallback($level);
        }
        
        // Get permission IDs from permission_groups table
        $permissionIds = DB::table('stoma_permission_groups')
            ->where('name', $level)
            ->where('status', 'Enable')
            ->value('permission');
        
        if (empty($permissionIds)) {
            return $this->getAllowedUrlsByLevelFallback($level);
        }
        
        // Split comma-separated permission IDs
        $ids = explode(',', $permissionIds);
        
        // Check if permission_access table exists
        if (!DB::getSchemaBuilder()->hasTable('stoma_permission_access')) {
            return $this->getAllowedUrlsByLevelFallback($level);
        }
        
        // Get URLs for these permission IDs
        $urls = DB::table('stoma_permission_access')
            ->whereIn('uniqueid', $ids)
            ->pluck('url')
            ->toArray();
        
        return $urls;
    }
    
    /**
     * Fallback method to get allowed URLs by level when tables don't exist.
     * 
     * @param string $level
     * @return array
     */
    protected function getAllowedUrlsByLevelFallback(string $level): array
    {
        // For Admin level, allow all URLs (return empty array means check passes)
        // For View level, only allow view URLs
        if ($level === 'Admin') {
            // Admin has access to all URLs, so we'll check at module level instead
            return [];
        }
        
        // View level: only allow view/list URLs
        $viewUrls = [
            'employee',
            'employee/view',
            'employeepayroll',
            'employeepayroll/view',
            'roster',
            'roster/view',
            'roster/viewweekroster',
            'clocktime',
            'clocktime/clockreport',
            'holidayrequest',
            'holidayrequest/view',
            'document',
            'resignation',
            'resignation/view',
            'employeereviews',
        ];
        
        return $viewUrls;
    }
}

