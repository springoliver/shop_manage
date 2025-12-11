<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreEmployee;
use App\Models\UserGroup;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Check if viewing ex-employees or active employees
        $viewType = $request->get('type', 'active'); // 'active' or 'ex'
        
        // Get employees based on view type - load all for client-side pagination
        $query = StoreEmployee::where('storeid', $storeid);
        
        if ($viewType === 'ex') {
            // Show only deactivated employees
            $employees = $query->where('status', 'Deactivate')
                ->orderBy('employeeid', 'DESC')
                ->get();
            $pageTitle = 'EX Employees';
            $toggleButtonText = 'Active Employees';
            $toggleButtonType = 'active';
        } else {
            // Show active employees (status != 'Deactivate')
            $employees = $query->where('status', '!=', 'Deactivate')
                ->orderBy('employeeid', 'DESC')
                ->get();
            $pageTitle = 'Active Employees';
            $toggleButtonText = 'Ex Employees';
            $toggleButtonType = 'ex';
        }
        
        return view('storeowner.employee.index', compact('employees', 'pageTitle', 'toggleButtonText', 'toggleButtonType'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get user groups for the current store
        $groups = DB::table('stoma_store_usergroup as su')
            ->join('stoma_usergroup as u', 'u.usergroupid', '=', 'su.usergroupid')
            ->where('su.storeid', $storeid)
            ->select('u.usergroupid', 'u.groupname')
            ->groupBy('u.usergroupid', 'u.groupname')
            ->get();
        
        // Get departments for the current store OR global (storeid = 0)
        $departments = DB::table('stoma_store_department')
            ->where(function($query) use ($storeid) {
                $query->where('storeid', $storeid)
                      ->orWhere('storeid', 0);
            })
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.employee.create', compact('groups', 'departments'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'groupid' => 'required|integer',
            'departmentid' => 'required|integer',
            'wroster' => 'required|integer',
            'droster' => 'required|integer',
            'every_hrs' => 'required|integer',
            'break_min' => 'required|integer',
            'paid_break' => 'required|in:Yes,No',
            'display_hrs_hols' => 'required|in:Yes,No',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'emailid' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'emptaxnumber' => 'required|string|max:51',
            'empnationality' => 'required|string|max:51',
            'empjoindate' => 'required|date',
            'empbankdetails1' => 'required|string|max:51',
            'empbankdetails2' => 'required|string|max:51',
            'phone' => 'required|string|max:51',
            'address' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address_state' => 'nullable|string|max:255',
            'address_country' => 'nullable|string|max:55',
            'country' => 'required|string|max:55',
            'address_city' => 'nullable|string|max:255',
            'address_zipcode' => 'nullable|string|max:21',
            'dateofbirth' => 'required|date',
            'payment_method' => 'required|in:hourly,weekly,fortnightly,lunar,monthly,yearly',
            'sallary_method' => 'required|in:hourly,yearly',
            'holiday_percent' => 'nullable|numeric',
            'holiday_day_entitiled' => 'nullable|numeric',
            'pay_rate_hour' => 'required|numeric',
            'pay_rate_week' => 'nullable|numeric',
            'pay_rate_year' => 'nullable|numeric',
            'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        $ip = $request->ip();
        
        // Generate random login code
        $random = rand(1000, 10000);
        
        // Handle profile photo upload
        $profilePhoto = null;
        if ($request->hasFile('profile_img')) {
            $file = $request->file('profile_img');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($validated['firstname'] . ' ' . $validated['lastname']) . date('YmdHis') . '.' . $extension;
            $profilePhoto = $file->storeAs('employees', $filename, 'public');
        }
        
        // Create employee
        $employee = new StoreEmployee();
        $employee->storeid = $storeid;
        $employee->usergroupid = $validated['groupid'];
        $employee->departmentid = $validated['departmentid'];
        $employee->roster_week_hrs = $validated['wroster'];
        $employee->roster_day_hrs = $validated['droster'];
        $employee->break_every_hrs = $validated['every_hrs'];
        $employee->break_min = $validated['break_min'];
        $employee->paid_break = $validated['paid_break'];
        $employee->display_hrs_hols = $validated['display_hrs_hols'];
        $employee->holiday_percent = $validated['holiday_percent'] ?? 0;
        $employee->holiday_day_entitiled = $validated['holiday_day_entitiled'] ?? 0;
        $employee->firstname = $validated['firstname'];
        $employee->lastname = $validated['lastname'];
        $employee->username = $validated['username'];
        $employee->emailid = $validated['emailid'];
        $employee->emptaxnumber = $validated['emptaxnumber'];
        $employee->empnationality = $validated['empnationality'];
        $employee->empjoindate = $validated['empjoindate'];
        $employee->empbankdetails1 = $validated['empbankdetails1'];
        $employee->empbankdetails2 = $validated['empbankdetails2'];
        $employee->emplogin_code = $random;
        // Store password as base64 for backward compatibility (CI uses base64)
        $employee->password = base64_encode($validated['password']);
        $employee->profile_photo = $profilePhoto;
        $employee->phone = $validated['phone'];
        $employee->country = $validated['address_country'] ?? $validated['country'] ?? '';
        $employee->address1 = $validated['address'];
        $employee->address2 = $validated['address1'];
        $employee->state = $validated['address_state'] ?? '';
        $employee->city = $validated['address_city'] ?? '';
        $employee->zipcode = $validated['address_zipcode'] ?? '';
        $employee->dateofbirth = $validated['dateofbirth'];
        $employee->accept_terms = 'Yes';
        $employee->payment_method = $validated['payment_method'];
        $employee->sallary_method = $validated['sallary_method'];
        $employee->pay_rate_hour = $validated['pay_rate_hour'];
        $employee->pay_rate_week = $validated['pay_rate_week'] ?? 0;
        $employee->pay_rate_year = $validated['pay_rate_year'] ?? 0;
        $employee->signupdate = now();
        $employee->signupip = $ip;
        $employee->signupby = 0;
        $employee->status = 'Active';
        $employee->save();
        
        // TODO: Send email notification with login code
        
        return redirect()->route('storeowner.employee.index')
            ->with('success', 'Employee added successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(string $employeeid): View
    {
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        // Get user group name
        $group = UserGroup::find($employee->usergroupid);
        
        return view('storeowner.employee.view', compact('employee', 'group'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(string $employeeid): View
    {
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get user groups for the current store
        $groups = DB::table('stoma_store_usergroup as su')
            ->join('stoma_usergroup as u', 'u.usergroupid', '=', 'su.usergroupid')
            ->where('su.storeid', $storeid)
            ->select('u.usergroupid', 'u.groupname')
            ->groupBy('u.usergroupid', 'u.groupname')
            ->get();
        
        // Get departments for the current store OR global (storeid = 0)
        $departments = DB::table('stoma_store_department')
            ->where(function($query) use ($storeid) {
                $query->where('storeid', $storeid)
                      ->orWhere('storeid', 0);
            })
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.employee.edit', compact('employee', 'groups', 'departments'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, string $employeeid): RedirectResponse
    {
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $validated = $request->validate([
            'groupid' => 'required|integer',
            'departmentid' => 'required|integer',
            'wroster' => 'required|integer',
            'droster' => 'required|integer',
            'every_hrs' => 'required|integer',
            'break_min' => 'required|integer',
            'paid_break' => 'required|in:Yes,No',
            'display_hrs_hols' => 'required|in:Yes,No',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'emailid' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'emptaxnumber' => 'required|string|max:51',
            'empnationality' => 'required|string|max:51',
            'empjoindate' => 'required|date',
            'empbankdetails1' => 'required|string|max:51',
            'empbankdetails2' => 'required|string|max:51',
            'phone' => 'required|string|max:51',
            'address' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address_state' => 'nullable|string|max:255',
            'address_country' => 'nullable|string|max:55',
            'country' => 'required|string|max:55',
            'address_city' => 'nullable|string|max:255',
            'address_zipcode' => 'nullable|string|max:21',
            'dateofbirth' => 'required|date',
            'payment_method' => 'required|in:hourly,weekly,fortnightly,lunar,monthly,yearly',
            'sallary_method' => 'required|in:hourly,yearly',
            'holiday_percent' => 'nullable|numeric',
            'holiday_day_entitiled' => 'nullable|numeric',
            'pay_rate_hour' => 'required|numeric',
            'pay_rate_week' => 'nullable|numeric',
            'pay_rate_year' => 'nullable|numeric',
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user = auth('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        $ip = $request->ip();
        
        // Handle profile photo upload
        if ($request->hasFile('profile_img')) {
            // Delete old photo if exists
            if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            
            $file = $request->file('profile_img');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($validated['firstname'] . ' ' . $validated['lastname']) . date('YmdHis') . '.' . $extension;
            $employee->profile_photo = $file->storeAs('employees', $filename, 'public');
        }
        
        // Update employee
        $employee->storeid = $storeid;
        $employee->usergroupid = $validated['groupid'];
        $employee->departmentid = $validated['departmentid'];
        $employee->roster_week_hrs = $validated['wroster'];
        $employee->roster_day_hrs = $validated['droster'];
        $employee->break_every_hrs = $validated['every_hrs'];
        $employee->break_min = $validated['break_min'];
        $employee->paid_break = $validated['paid_break'];
        $employee->display_hrs_hols = $validated['display_hrs_hols'];
        $employee->holiday_percent = $validated['holiday_percent'] ?? 0;
        $employee->holiday_day_entitiled = $validated['holiday_day_entitiled'] ?? 0;
        $employee->firstname = $validated['firstname'];
        $employee->lastname = $validated['lastname'];
        $employee->username = $validated['username'];
        $employee->emailid = $validated['emailid'];
        $employee->emptaxnumber = $validated['emptaxnumber'];
        $employee->empnationality = $validated['empnationality'];
        $employee->empjoindate = $validated['empjoindate'];
        $employee->empbankdetails1 = $validated['empbankdetails1'];
        $employee->empbankdetails2 = $validated['empbankdetails2'];
        
        // Update password only if provided
        if (!empty($validated['password'])) {
            $employee->password = base64_encode($validated['password']);
        }
        
        $employee->phone = $validated['phone'];
        $employee->country = $validated['address_country'] ?? $validated['country'] ?? $employee->country;
        $employee->address1 = $validated['address'];
        $employee->address2 = $validated['address1'];
        $employee->state = $validated['address_state'] ?? $employee->state ?? '';
        $employee->city = $validated['address_city'] ?? $employee->city ?? '';
        $employee->zipcode = $validated['address_zipcode'] ?? $employee->zipcode ?? '';
        $employee->dateofbirth = $validated['dateofbirth'];
        $employee->payment_method = $validated['payment_method'];
        $employee->sallary_method = $validated['sallary_method'];
        $employee->pay_rate_hour = $validated['pay_rate_hour'];
        $employee->pay_rate_week = $validated['pay_rate_week'] ?? 0;
        $employee->pay_rate_year = $validated['pay_rate_year'] ?? 0;
        $employee->editdate = now();
        $employee->editip = $ip;
        $employee->editby = 0;
        $employee->save();
        
        return redirect()->route('storeowner.employee.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(string $employeeid): RedirectResponse
    {
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        // Delete profile photo if exists
        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }
        
        // Actually delete the employee record
        $employee->delete();
        
        return redirect()->route('storeowner.employee.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Change employee status.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employeeid' => 'required|string',
            'status' => 'required|in:Active,Deactivate,Suspended',
        ]);
        
        $employeeid = base64_decode($validated['employeeid']);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $employee->status = $validated['status'];
        $employee->save();
        
        return redirect()->route('storeowner.employee.index')
            ->with('success', 'Employee status changed successfully.');
    }

    /**
     * Check if email exists (AJAX).
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email') ?? $request->input('emailid');
        $employeeid = $request->input('employeeid');
        
        $query = StoreEmployee::where('emailid', $email);
        
        // Exclude current employee if editing
        if ($employeeid) {
            $decodedId = base64_decode($employeeid);
            $query->where('employeeid', '!=', $decodedId);
        }
        
        $exists = $query->exists();
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Check if username exists (AJAX).
     */
    public function checkUsername(Request $request)
    {
        $username = $request->input('username');
        $employeeid = $request->input('employeeid');
        
        $query = StoreEmployee::where('username', $username);
        
        // Exclude current employee if editing
        if ($employeeid) {
            $decodedId = base64_decode($employeeid);
            $query->where('employeeid', '!=', $decodedId);
        }
        
        $exists = $query->exists();
        
        return response()->json(['exists' => $exists]);
    }
}
