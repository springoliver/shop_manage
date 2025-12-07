<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $userGroups = UserGroup::with('store')
            ->orderBy('usergroupid', 'desc')
            ->get();

        return view('admin.user-groups.index', compact('userGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::where('status', 'Active')->orderBy('store_name')->get();
        return view('admin.user-groups.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'groupname' => 'required|string|max:255',
            'level_access' => 'required|in:Admin,View',
            'storeid' => 'required|exists:stoma_store,storeid',
        ]);

        UserGroup::create([
            'groupname' => $validated['groupname'],
            'level_access' => $validated['level_access'],
            'storeid' => $validated['storeid'],
            'status' => 'Enable',
        ]);

        return redirect()->route('admin.user-groups.index')
            ->with('success', 'User Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserGroup $userGroup): View
    {
        $userGroup->load('store');
        return view('admin.user-groups.show', compact('userGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserGroup $userGroup): View
    {
        $stores = Store::where('status', 'Active')->orderBy('store_name')->get();
        return view('admin.user-groups.edit', compact('userGroup', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserGroup $userGroup): RedirectResponse
    {
        $validated = $request->validate([
            'groupname' => 'required|string|max:255',
            'level_access' => 'required|in:Admin,View',
            'storeid' => 'required|exists:stoma_store,storeid',
        ]);

        $userGroup->update($validated);

        return redirect()->route('admin.user-groups.index')
            ->with('success', 'User Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserGroup $userGroup): RedirectResponse
    {
        // Check if user group is assigned to any employees
       
        // $employeeCount = DB::table('employee')
        //     ->where('usergroupid', $userGroup->usergroupid)
        //     ->count();

        // if ($employeeCount > 0) {
        //     return redirect()->route('admin.user-groups.index')
        //         ->with('error', 'User Group cannot be deleted. It has been assigned to employees.');
        // }

        $userGroup->delete();

        return redirect()->route('admin.user-groups.index')
            ->with('success', 'User Group deleted successfully.');
    }

    /**
     * Change the status of the user group.
     */
    public function changeStatus(Request $request, UserGroup $userGroup): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Enable,Disable',
        ]);

        $userGroup->update(['status' => $validated['status']]);

        return redirect()->route('admin.user-groups.index')
            ->with('success', 'Status changed successfully.');
    }

    /**
     * Check if group name is available (AJAX).
     */
    public function checkGroupNameAvailability(Request $request)
    {
        $groupname = $request->input('groupname');
        $usergroupid = $request->input('usergroupid');

        $query = UserGroup::where('groupname', $groupname);

        if ($usergroupid) {
            $query->where('usergroupid', '!=', $usergroupid);
        }

        $exists = $query->exists();

        return response()->json($exists ? 1 : 0);
    }
}

