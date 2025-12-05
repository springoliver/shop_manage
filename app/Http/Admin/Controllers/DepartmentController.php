<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Store;
use App\Models\StoreType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $departments = Department::with(['storeType', 'store'])
            ->orderBy('departmentid', 'desc')
            ->get();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        $stores = Store::where('status', 'Active')->orderBy('store_name')->get();
        return view('admin.departments.create', compact('storeTypes', 'stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'storetypeid' => 'required|exists:stoma_storetype,typeid',
            'storeid' => 'required|exists:stoma_store,storeid',
            'roster_max_time' => 'nullable|integer|min:0',
        ]);

        Department::create([
            'department' => $validated['department'],
            'storetypeid' => $validated['storetypeid'],
            'storeid' => $validated['storeid'],
            'roster_max_time' => $validated['roster_max_time'] ?? 0,
            'status' => 'Enable',
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department): View
    {
        $department->load(['storeType', 'store']);
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): View
    {
        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        $stores = Store::where('status', 'Active')->orderBy('store_name')->get();
        return view('admin.departments.edit', compact('department', 'storeTypes', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'storetypeid' => 'required|exists:stoma_storetype,typeid',
            'storeid' => 'required|exists:stoma_store,storeid',
            'roster_max_time' => 'nullable|integer|min:0',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Change the status of the department.
     */
    public function changeStatus(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Enable,Disable',
        ]);

        $department->update(['status' => $validated['status']]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Status changed successfully.');
    }

    /**
     * Check if department name is available (AJAX).
     */
    public function checkDepartmentAvailability(Request $request)
    {
        $department = $request->input('department');
        $departmentid = $request->input('departmentid');

        $query = Department::where('department', $department);

        if ($departmentid) {
            $query->where('departmentid', '!=', $departmentid);
        }

        $exists = $query->exists();

        return response()->json($exists ? 1 : 0);
    }
}

