<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the user groups.
     */
    public function index(): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($storeid == 0) {
            $userGroups = collect([]);
        } else {
            // Get user groups from store_usergroup table joined with usergroup
            // Similar to CI's get_usergroup method
            $userGroups = DB::table('stoma_store_usergroup as su')
                ->join('stoma_usergroup as u', 'u.usergroupid', '=', 'su.usergroupid')
                ->leftJoin('stoma_module_access as ma', function($join) {
                    $join->on('ma.usergroupid', '=', 'su.usergroupid')
                         ->on('ma.storeid', '=', 'su.storeid');
                })
                ->where('su.storeid', $storeid)
                ->select('su.suid', 'su.storeid', 'su.usergroupid', 'su.hour_charge', 'su.total_week_hour', 'su.insertdatetime', 'su.insertip', 'su.editdatetime', 'su.editip', 'u.groupname', 'u.storeid as usergroup_storeid')
                ->groupBy('su.suid', 'su.storeid', 'su.usergroupid', 'su.hour_charge', 'su.total_week_hour', 'su.insertdatetime', 'su.insertip', 'su.editdatetime', 'su.editip', 'u.groupname', 'u.storeid')
                ->get()
                ->map(function($item) {
                    return (object) [
                        'usergroupid' => $item->usergroupid,
                        'storeid' => $item->storeid,
                        'groupname' => $item->groupname,
                        'usergroup_storeid' => $item->usergroup_storeid,
                        'hour_charge' => $item->hour_charge,
                        'total_week_hour' => $item->total_week_hour,
                    ];
                });
        }
        
        return view('storeowner.usergroup.index', compact('userGroups'));
    }

    /**
     * Show the form for creating a new user group.
     */
    public function create(): View
    {
        return view('storeowner.usergroup.create');
    }

    /**
     * Store a newly created user group.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($storeid == 0) {
            return redirect()->route('storeowner.usergroup.index')
                ->with('error', 'Please select a store first.');
        }

        $validated = $request->validate([
            'ugname' => ['required', 'string', 'max:255'],
        ]);

        // Check if user group name already exists for this store or globally
        $exists = UserGroup::where('groupname', $validated['ugname'])
            ->where(function($query) use ($storeid) {
                $query->where('storeid', 0)
                      ->orWhere('storeid', $storeid);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'User Group name already exists.');
        }

        // Create user group
        $userGroup = UserGroup::create([
            'storeid' => $storeid,
            'groupname' => $validated['ugname'],
            'status' => 'Enable',
            'level_access' => 'View', // Default value
        ]);

        $usergroupid = $userGroup->usergroupid;

        // Create entry in store_usergroup table
        DB::table('stoma_store_usergroup')->insert([
            'storeid' => $storeid,
            'usergroupid' => $usergroupid,
            'hour_charge' => '0.00',
            'total_week_hour' => 0,
            'insertdatetime' => now(),
            'insertip' => $request->ip(),
            'editdatetime' => null,
            'editip' => null,
        ]);

        // Get all modules and create module_access entries
        $modules = Module::all();
        foreach ($modules as $module) {
            DB::table('stoma_module_access')->insert([
                'storeid' => $storeid,
                'usergroupid' => $usergroupid,
                'moduleid' => $module->moduleid,
                'level' => 'None',
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
        }

        return redirect()->route('storeowner.usergroup.index')
            ->with('success', 'User Group created successfully.');
    }

    /**
     * Show the form for editing a user group.
     */
    public function edit($usergroup): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the user group since route model binding might not work with custom route key
        $userGroup = UserGroup::where('usergroupid', $usergroup)->first();
        
        if (!$userGroup) {
            abort(404, 'User Group not found.');
        }
        
        // Ensure the user group belongs to the current store
        if ($userGroup->storeid != $storeid && $userGroup->storeid != 0) {
            abort(403, 'Unauthorized access.');
        }

        // Get module access data for this user group
        // Similar to CI's get_moduleaccessdata - join with paid_module to check if modules are active
        $curDate = date('Y-m-d');
        
        // Get modules from paid_module (active modules) joined with module_access
        $modules = DB::table('stoma_paid_module as pm')
            ->join('stoma_module as m', 'm.moduleid', '=', 'pm.moduleid')
            ->leftJoin('stoma_module_access as ma', function($join) use ($storeid, $userGroup) {
                $join->on('ma.moduleid', '=', 'pm.moduleid')
                     ->where('ma.storeid', '=', $storeid)
                     ->where('ma.usergroupid', '=', $userGroup->usergroupid);
            })
            ->where('pm.storeid', $storeid)
            ->whereDate('pm.purchase_date', '<=', $curDate)
            ->whereDate('pm.expire_date', '>=', $curDate)
            ->select('ma.level', 'm.module', 'm.moduleid')
            ->groupBy('m.moduleid', 'ma.level', 'm.module')
            ->get()
            ->map(function($item) use ($storeid, $userGroup) {
                return (object) [
                    'moduleid' => $item->moduleid,
                    'module' => $item->module,
                    'level' => $item->level ?? 'None',
                    'storeid' => $storeid,
                    'usergroupid' => $userGroup->usergroupid,
                ];
            });

        // If no modules found from paid_module, get from module_access directly
        if ($modules->isEmpty()) {
            $modules = DB::table('stoma_module_access as ma')
                ->join('stoma_module as m', 'm.moduleid', '=', 'ma.moduleid')
                ->where('ma.storeid', $storeid)
                ->where('ma.usergroupid', $userGroup->usergroupid)
                ->select('ma.*', 'm.module', 'm.moduleid')
                ->get();
        }

        return view('storeowner.usergroup.edit', compact('userGroup', 'modules'));
    }

    /**
     * Update module access levels for a user group.
     */
    public function update(Request $request, $usergroup): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the user group since route model binding might not work with custom route key
        $userGroup = UserGroup::where('usergroupid', $usergroup)->first();
        
        if (!$userGroup) {
            abort(404, 'User Group not found.');
        }
        
        // Ensure the user group belongs to the current store
        if ($userGroup->storeid != $storeid && $userGroup->storeid != 0) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'totalmodule' => ['required', 'integer'],
            'accesslevel' => ['required', 'array'],
            'accesslevel.*' => ['required', 'in:Admin,View,None'],
        ]);

        // Update module access levels
        for ($i = 0; $i < $validated['totalmodule']; $i++) {
            $moduleid = $request->input('hdnmodule' . $i);
            $level = $validated['accesslevel'][$i] ?? 'None';

            DB::table('stoma_module_access')
                ->where('storeid', $storeid)
                ->where('usergroupid', $userGroup->usergroupid)
                ->where('moduleid', $moduleid)
                ->update(['level' => $level]);
        }

        return redirect()->route('storeowner.usergroup.index')
            ->with('success', 'Module settings updated successfully.');
    }

    /**
     * Remove the specified user group.
     */
    public function destroy($usergroup): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the user group since route model binding might not work with custom route key
        $userGroup = UserGroup::where('usergroupid', $usergroup)->first();
        
        if (!$userGroup) {
            abort(404, 'User Group not found.');
        }
        
        // Ensure the user group belongs to the current store
        if ($userGroup->storeid != $storeid) {
            abort(403, 'Unauthorized access. Cannot delete global user groups.');
        }

        // Delete from store_usergroup
        DB::table('stoma_store_usergroup')
            ->where('storeid', $storeid)
            ->where('usergroupid', $userGroup->usergroupid)
            ->delete();

        // Delete from module_access
        DB::table('stoma_module_access')
            ->where('storeid', $storeid)
            ->where('usergroupid', $userGroup->usergroupid)
            ->delete();

        // Delete the user group
        $userGroup->delete();

        return redirect()->route('storeowner.usergroup.index')
            ->with('success', 'User Group deleted successfully.');
    }

    /**
     * View module access data (AJAX).
     */
    public function view(Request $request)
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $usergroupid = $request->input('usergroupid');
        
        // Get user group name
        $userGroup = UserGroup::find($usergroupid);
        $groupname = $userGroup ? $userGroup->groupname : 'User Group';
        
        // Get module access data
        $modules = DB::table('stoma_module_access as ma')
            ->join('stoma_module as m', 'm.moduleid', '=', 'ma.moduleid')
            ->where('ma.storeid', $storeid)
            ->where('ma.usergroupid', $usergroupid)
            ->select('ma.*', 'm.module', 'm.moduleid')
            ->get();

        return view('storeowner.usergroup.view', compact('modules', 'groupname'))->render();
    }

    /**
     * Check if user group name is available (AJAX).
     */
    public function checkName(Request $request)
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $groupname = $request->input('ugname');
        
        $exists = UserGroup::where('groupname', $groupname)
            ->where(function($query) use ($storeid) {
                $query->where('storeid', 0)
                      ->orWhere('storeid', $storeid);
            })
            ->exists();
        
        return response($exists ? '1' : '0');
    }
}

