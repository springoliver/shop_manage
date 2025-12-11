<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Store;
use App\Models\StoreType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index(Request $request): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get departments for current store OR global (storeid = 0)
        // Similar to CI's get_all_department method
        $query = DB::table('stoma_store_department as d')
            ->leftJoin('stoma_storetype as st', 'st.typeid', '=', 'd.storetypeid')
            ->where(function($q) use ($storeid) {
                $q->where('d.storeid', $storeid)
                  ->orWhere('d.storeid', 0);
            });
        
        $query->select('d.*', 'st.typeid', 'st.store_type')
            ->orderBy('d.departmentid', 'DESC');
        
        $departments = $query->get()
            ->map(function($item) {
                return (object) [
                    'departmentid' => $item->departmentid,
                    'department' => $item->department,
                    'storetypeid' => $item->storetypeid,
                    'storeid' => $item->storeid,
                    'store_type' => $item->store_type ?? 'N/A',
                    'roster_max_time' => $item->roster_max_time,
                    'day_max_time' => $item->day_max_time,
                    'target_hours' => $item->target_hours,
                    'status' => $item->status,
                ];
            });
        
        return view('storeowner.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get current store's type for default selection
        $currentStoreType = DB::table('stoma_store as s')
            ->join('stoma_storetype as st', 'st.typeid', '=', 's.typeid')
            ->where('st.status', 'Enable')
            ->where('s.storeid', $storeid)
            ->select('st.typeid', 'st.store_type')
            ->first();
        
        // Get all enabled store types for dropdown
        $storeTypes = StoreType::where('status', 'Enable')->get();
        
        return view('storeowner.department.create', compact('currentStoreType', 'storeTypes'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($storeid == 0) {
            return redirect()->route('storeowner.department.index')
                ->with('error', 'Please select a store first.');
        }

        $validated = $request->validate([
            'department' => ['required', 'string', 'max:255'],
            'storeid' => ['required', 'integer'],
            'wroster' => ['required', 'integer'],
            'droster' => ['required', 'integer'],
            'target_hours' => ['required', 'integer'],
            'Monday' => ['nullable', 'integer'],
            'Tuesday' => ['nullable', 'integer'],
            'Wednesday' => ['nullable', 'integer'],
            'Thursday' => ['nullable', 'integer'],
            'Friday' => ['nullable', 'integer'],
            'Saturday' => ['nullable', 'integer'],
            'Sunday' => ['nullable', 'integer'],
        ]);

        // Check if department name already exists for this store
        $exists = Department::where('department', $validated['department'])
            ->where('storeid', $storeid)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Department name already exists.');
        }

        // Create department
        Department::create([
            'department' => $validated['department'],
            'storetypeid' => $validated['storeid'],
            'storeid' => $storeid,
            'roster_max_time' => $validated['wroster'],
            'day_max_time' => $validated['droster'],
            'target_hours' => $validated['target_hours'],
            'Monday' => $validated['Monday'] ?? 0,
            'Tuesday' => $validated['Tuesday'] ?? 0,
            'Wednesday' => $validated['Wednesday'] ?? 0,
            'Thursday' => $validated['Thursday'] ?? 0,
            'Friday' => $validated['Friday'] ?? 0,
            'Saturday' => $validated['Saturday'] ?? 0,
            'Sunday' => $validated['Sunday'] ?? 0,
            'status' => 'Enable',
        ]);

        return redirect()->route('storeowner.department.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show the form for editing a department.
     */
    public function edit($departmentid): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the department
        $department = Department::where('departmentid', $departmentid)->first();
        
        if (!$department) {
            abort(404, 'Department not found.');
        }
        
        // Ensure the department belongs to the current store (or is global)
        if ($department->storeid != $storeid && $department->storeid != 0) {
            abort(403, 'Unauthorized access.');
        }

        // Get all store types for dropdown (in edit, user can change store type)
        $storeTypes = StoreType::where('status', 'Enable')->get();

        return view('storeowner.department.edit', compact('department', 'storeTypes'));
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $departmentid): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the department
        $department = Department::where('departmentid', $departmentid)->first();
        
        if (!$department) {
            abort(404, 'Department not found.');
        }
        
        // Ensure the department belongs to the current store (or is global)
        if ($department->storeid != $storeid && $department->storeid != 0) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'department' => ['required', 'string', 'max:255'],
            'storeid' => ['required', 'integer'],
            'wroster' => ['required', 'integer'],
            'droster' => ['required', 'integer'],
            'target_hours' => ['required', 'integer'],
            'Monday' => ['nullable', 'integer'],
            'Tuesday' => ['nullable', 'integer'],
            'Wednesday' => ['nullable', 'integer'],
            'Thursday' => ['nullable', 'integer'],
            'Friday' => ['nullable', 'integer'],
            'Saturday' => ['nullable', 'integer'],
            'Sunday' => ['nullable', 'integer'],
        ]);

        // Check if department name already exists for this store (excluding current department)
        $exists = Department::where('department', $validated['department'])
            ->where('storeid', $storeid)
            ->where('departmentid', '!=', $departmentid)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Department name already exists.');
        }

        // Update department
        $department->update([
            'department' => $validated['department'],
            'storetypeid' => $validated['storeid'],
            'roster_max_time' => $validated['wroster'],
            'day_max_time' => $validated['droster'],
            'target_hours' => $validated['target_hours'],
            'Monday' => $validated['Monday'] ?? 0,
            'Tuesday' => $validated['Tuesday'] ?? 0,
            'Wednesday' => $validated['Wednesday'] ?? 0,
            'Thursday' => $validated['Thursday'] ?? 0,
            'Friday' => $validated['Friday'] ?? 0,
            'Saturday' => $validated['Saturday'] ?? 0,
            'Sunday' => $validated['Sunday'] ?? 0,
        ]);

        return redirect()->route('storeowner.department.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department.
     */
    public function destroy($departmentid): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Manually resolve the department
        $department = Department::where('departmentid', $departmentid)->first();
        
        if (!$department) {
            abort(404, 'Department not found.');
        }
        
        // Only allow deletion of store-specific departments (not global ones with storeid = 0)
        if ($department->storeid != $storeid) {
            abort(403, 'Unauthorized access. Cannot delete global departments.');
        }

        $department->delete();

        return redirect()->route('storeowner.department.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Change the status of a department.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $validated = $request->validate([
            'departmentid' => ['required'],
            'status' => ['required', 'in:Enable,Disable'],
        ]);

        $departmentid = base64_decode($validated['departmentid']);
        
        $department = Department::where('departmentid', $departmentid)->first();
        
        if (!$department) {
            return redirect()->route('storeowner.department.index')
                ->with('error', 'Department not found.');
        }
        
        // Only allow status change for store-specific departments
        if ($department->storeid != $storeid) {
            return redirect()->route('storeowner.department.index')
                ->with('error', 'Unauthorized access.');
        }

        $department->update(['status' => $validated['status']]);

        return redirect()->route('storeowner.department.index')
            ->with('success', 'Status changed successfully.');
    }

    /**
     * Check if department name is available (AJAX).
     */
    public function checkName(Request $request)
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $department = $request->input('department');
        $departmentid = $request->input('departmentid');
        
        $query = Department::where('department', $department)
            ->where('storeid', $storeid);
        
        if ($departmentid) {
            $decodedId = base64_decode($departmentid);
            if ($decodedId) {
                $query->where('departmentid', '!=', $decodedId);
            }
        }
        
        $exists = $query->exists();
        
        return response($exists ? '1' : '0');
    }
}
