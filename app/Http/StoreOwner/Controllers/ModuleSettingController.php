<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\PaidModule;
use App\Models\Store;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class ModuleSettingController extends Controller
{
    /**
     * Display a listing of the modules.
     */
    public function index(): View
    {
        $user = auth('storeowner')->user();
        $ownerid = $user->ownerid;
        $storeid = session('storeid', 0);
        
        // Get all stores for this owner (for multi-store check)
        $multistore = Store::where('storeownerid', $ownerid)
            ->where('status', 'Active')
            ->get();
        $storecount = $multistore->count();
        
        // Get installed modules (active, not expired)
        $curDate = date('Y-m-d');
        $installModule = PaidModule::with('module')
            ->where('storeid', $storeid)
            ->whereDate('purchase_date', '<=', $curDate)
            ->whereDate('expire_date', '>=', $curDate)
            ->get()
            ->map(function($pm) {
                return [
                    'moduleid' => $pm->moduleid,
                    'module' => $pm->module->module ?? '',
                    'price_1months' => $pm->module->price_1months ?? 0,
                    'module_description' => $pm->module->module_description ?? '',
                    'module_detailed_info' => $pm->module->module_detailed_info ?? '',
                    'expire_date' => $pm->expire_date,
                    'isTrial' => $pm->isTrial,
                ];
            })
            ->toArray();
        
        // Get all modules
        $allModule = Module::get()->toArray();
        
        // Get all installed modules (latest by insertdatetime, grouped by moduleid)
        $allinstallModule = DB::select("
            SELECT pm1.* 
            FROM stoma_paid_module pm1
            JOIN (
                SELECT moduleid, MAX(insertdatetime) as timestamp 
                FROM stoma_paid_module 
                WHERE storeid = ?
                GROUP BY moduleid
            ) pm2 
            ON pm1.moduleid = pm2.moduleid 
            AND pm1.insertdatetime = pm2.timestamp 
            WHERE pm1.storeid = ?
        ", [$storeid, $storeid]);
        
        // Calculate time differences for "Last Updated"
        $diff = [];
        if (count($allinstallModule) > 0) {
            foreach ($allinstallModule as $i => $pm) {
                $startdate = Carbon::now();
                $enddate = Carbon::parse($pm->insertdatetime);
                $interval = $startdate->diff($enddate);
                
                if ($interval->y > 0) {
                    $diff[] = $interval->y . ' Year ago';
                } elseif ($interval->m > 0) {
                    $diff[] = $interval->m . ' Month ago';
                } elseif ($interval->d > 0) {
                    $diff[] = $interval->d . ' days ago';
                } elseif ($interval->h > 0) {
                    $diff[] = $interval->h . ' hour ago';
                } elseif ($interval->i > 0) {
                    $diff[] = $interval->i . ' minute ago';
                } else {
                    $diff[] = '';
                }
            }
        }
        
        // Get cart modules (if cart table exists)
        $cartModule = [];
        try {
            $cartModules = DB::table('stoma_cart')
                ->where('store_id', $storeid)
                ->where('owner_id', $ownerid)
                ->pluck('module_id')
                ->toArray();
            $cartModule = $cartModules;
        } catch (\Exception $e) {
            // Cart table might not exist yet
            $cartModule = [];
        }
        
        return view('storeowner.modulesetting.index', compact(
            'installModule',
            'allModule',
            'allinstallModule',
            'diff',
            'storecount',
            'cartModule'
        ));
    }

    /**
     * View module access for a user group (AJAX).
     */
    public function view(Request $request)
    {
        $usergroupid = $request->input('usergroupid');
        $storeid = session('storeid', 0);
        
        // Get module access data for this user group
        $curDate = date('Y-m-d');
        
        // Get user group name
        $userGroup = UserGroup::find($usergroupid);
        if (!$userGroup) {
            return response()->json(['error' => 'User group not found'], 404);
        }
        
        $usergroup = DB::table('stoma_paid_module as pm')
            ->join('stoma_module as m', 'm.moduleid', '=', 'pm.moduleid')
            ->leftJoin('stoma_module_access as ma', function($join) use ($storeid, $usergroupid) {
                $join->on('ma.moduleid', '=', 'pm.moduleid')
                     ->where('ma.storeid', '=', $storeid)
                     ->where('ma.usergroupid', '=', $usergroupid);
            })
            ->where('pm.storeid', $storeid)
            ->whereDate('pm.purchase_date', '<=', $curDate)
            ->whereDate('pm.expire_date', '>=', $curDate)
            ->select('ma.level', 'm.module', 'm.moduleid')
            ->groupBy('m.moduleid', 'ma.level', 'm.module')
            ->get()
            ->map(function($item) use ($userGroup) {
                return (object) [
                    'moduleid' => $item->moduleid,
                    'module' => $item->module,
                    'level' => $item->level ?? 'None',
                    'groupname' => $userGroup->groupname,
                    'usergroupid' => $userGroup->usergroupid,
                ];
            });
        
        // If no modules found from paid_module, get from module_access directly
        if ($usergroup->isEmpty()) {
            $usergroup = DB::table('stoma_module_access as ma')
                ->join('stoma_module as m', 'm.moduleid', '=', 'ma.moduleid')
                ->where('ma.storeid', $storeid)
                ->where('ma.usergroupid', $usergroupid)
                ->select('ma.level', 'm.module', 'm.moduleid')
                ->get()
                ->map(function($item) use ($userGroup) {
                    return (object) [
                        'moduleid' => $item->moduleid,
                        'module' => $item->module,
                        'level' => $item->level ?? 'None',
                        'groupname' => $userGroup->groupname,
                        'usergroupid' => $userGroup->usergroupid,
                    ];
                });
        }
        
        return view('storeowner.modulesetting.view', compact('usergroup'));
    }

    /**
     * Show the form for editing module access levels.
     */
    public function edit($usergroupid): View
    {
        $usergroupid = base64_decode($usergroupid);
        $storeid = session('storeid', 0);
        
        // Get user group
        $userGroup = UserGroup::find($usergroupid);
        if (!$userGroup) {
            abort(404, 'User group not found');
        }
        
        // Get module access data for this user group
        $curDate = date('Y-m-d');
        
        $usergroup = DB::table('stoma_paid_module as pm')
            ->join('stoma_module as m', 'm.moduleid', '=', 'pm.moduleid')
            ->leftJoin('stoma_module_access as ma', function($join) use ($storeid, $usergroupid) {
                $join->on('ma.moduleid', '=', 'pm.moduleid')
                     ->where('ma.storeid', '=', $storeid)
                     ->where('ma.usergroupid', '=', $usergroupid);
            })
            ->where('pm.storeid', $storeid)
            ->whereDate('pm.purchase_date', '<=', $curDate)
            ->whereDate('pm.expire_date', '>=', $curDate)
            ->select('ma.level', 'm.module', 'm.moduleid')
            ->groupBy('m.moduleid', 'ma.level', 'm.module')
            ->get()
            ->map(function($item) use ($userGroup) {
                return (object) [
                    'moduleid' => $item->moduleid,
                    'module' => $item->module,
                    'level' => $item->level ?? 'None',
                    'groupname' => $userGroup->groupname,
                    'usergroupid' => $userGroup->usergroupid,
                ];
            });
        
        // If no modules found from paid_module, get from module_access directly
        if ($usergroup->isEmpty()) {
            $usergroup = DB::table('stoma_module_access as ma')
                ->join('stoma_module as m', 'm.moduleid', '=', 'ma.moduleid')
                ->where('ma.storeid', $storeid)
                ->where('ma.usergroupid', $usergroupid)
                ->select('ma.level', 'm.module', 'm.moduleid')
                ->get()
                ->map(function($item) use ($userGroup) {
                    return (object) [
                        'moduleid' => $item->moduleid,
                        'module' => $item->module,
                        'level' => $item->level ?? 'None',
                        'groupname' => $userGroup->groupname,
                        'usergroupid' => $userGroup->usergroupid,
                    ];
                });
        }
        
        $title = $userGroup->groupname;
        
        return view('storeowner.modulesetting.edit', compact('usergroup', 'title'));
    }

    /**
     * Update module access levels.
     */
    public function update(Request $request): RedirectResponse
    {
        $usergroupid = base64_decode($request->input('usergroupid'));
        $totalmodule = $request->input('totalmodule');
        $accesslevel = $request->input('accesslevel', []);
        
        $storeid = session('storeid', 0);
        
        // Get all module IDs
        $moduleids = [];
        for ($i = 0; $i < $totalmodule; $i++) {
            $moduleids[] = $request->input('hdnmodule' . $i);
        }
        
        // Update module access levels
        for ($i = 0; $i < count($moduleids); $i++) {
            if (isset($moduleids[$i]) && isset($accesslevel[$i])) {
                DB::table('stoma_module_access')
                    ->where('moduleid', $moduleids[$i])
                    ->where('usergroupid', $usergroupid)
                    ->where('storeid', $storeid)
                    ->update([
                        'level' => $accesslevel[$i],
                        'editdate' => now(),
                        'editip' => $request->ip(),
                    ]);
            }
        }
        
        return redirect()->route('storeowner.modulesetting.index')
            ->with('success', 'Module Setting Updated Successfully.');
    }

    /**
     * Handle module installation request.
     */
    public function install(Request $request): RedirectResponse
    {
        $moduleid = base64_decode($request->input('moduleid'));
        $status = $request->input('status'); // For multi-store discount
        $install = $request->input('install'); // For single store
        
        $storeid = session('storeid', 0);
        $user = auth('storeowner')->user();
        
        if ($install !== 'Yes' && $status !== 'Yes') {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('info', 'Installation cancelled.');
        }
        
        // Check if module exists
        $module = Module::find($moduleid);
        if (!$module) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Module not found.');
        }
        
        // Check if module is already installed (active, not expired)
        $curDate = date('Y-m-d');
        $existingPaidModule = PaidModule::where('storeid', $storeid)
            ->where('moduleid', $moduleid)
            ->whereDate('purchase_date', '<=', $curDate)
            ->whereDate('expire_date', '>=', $curDate)
            ->first();
        
        if ($existingPaidModule) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('info', 'Module is already installed.');
        }
        
        // Calculate purchase and expire dates
        $purchaseDate = Carbon::now()->startOfDay();
        
        // If module has free_days, use that; otherwise default to 30 days (1 month)
        if ($module->free_days > 0) {
            $expireDate = Carbon::now()->addDays($module->free_days)->endOfDay();
            $isTrial = 1;
            $paidAmount = '0.00';
        } else {
            // Default to 30 days (1 month) - can be changed to payment flow later
            $expireDate = Carbon::now()->addDays(30)->endOfDay();
            $isTrial = 0;
            $paidAmount = $module->price_1months ?? '0.00';
        }
        
        // Apply discount for multi-store owners (20% discount)
        if ($status === 'Yes') {
            // Multi-store discount logic
            $multistore = Store::where('storeownerid', $user->ownerid)
                ->where('status', 'Active')
                ->count();
            
            if ($multistore > 1 && $paidAmount > 0) {
                $discount = ($paidAmount * 20) / 100;
                $paidAmount = $paidAmount - $discount;
            }
        }
        
        // Create paid_module entry
        PaidModule::create([
            'storeid' => $storeid,
            'moduleid' => $moduleid,
            'purchase_date' => $purchaseDate,
            'expire_date' => $expireDate,
            'paid_amount' => $paidAmount,
            'status' => 'Enable',
            'insertdatetime' => now(),
            'insertip' => $request->ip(),
            'isTrial' => $isTrial,
        ]);
        
        // TODO: Send email notification (similar to CI's buymodule controller)
        // TODO: Implement payment gateway integration for paid modules
        
        return redirect()->route('storeowner.modulesetting.index')
            ->with('success', 'Module installed successfully.');
    }
}
