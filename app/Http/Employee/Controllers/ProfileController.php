<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the employee's profile (VIEW page - read-only).
     * Matches CI's my_profile/index functionality.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get user group name
        $group = \DB::table('stoma_usergroup')
            ->where('usergroupid', $employee->usergroupid)
            ->first();
        
        return view('employee.profile.view', [
            'employee' => $employee,
            'group' => $group,
        ]);
    }

    /**
     * Display the employee's profile edit form.
     * Matches CI's dashboard/editprofile functionality.
     */
    public function edit(Request $request): View
    {
        $employee = Auth::guard('employee')->user();
        
        return view('employee.profile.edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update the employee's profile information.
     * Matches CI's dashboard/update functionality.
     */
    public function update(Request $request): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'uname' => 'required|string|max:255',
            'emailid' => 'required|email|max:255',
            'phone' => 'required|string|max:51',
            'address' => 'required|string|max:255',
            'dateofbirth' => 'required|date', // HTML5 date input sends yyyy-mm-dd format
            'upimg' => 'required|in:Yes,No',
            'logo_img' => 'nullable|required_if:upimg,Yes|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Address fields from Google Places
            'address_formatted_address' => 'nullable|string|max:255',
            'address_state' => 'nullable|string|max:255',
            'address_country' => 'nullable|string|max:55',
            'address_city' => 'nullable|string|max:255',
            'address_zipcode' => 'nullable|string|max:21',
        ]);
        
        // Parse date - HTML5 date input sends yyyy-mm-dd format, but also handle dd-mm-yyyy for backward compatibility
        try {
            // Try yyyy-mm-dd format first (HTML5 date input)
            $dateOfBirth = Carbon::createFromFormat('Y-m-d', $validated['dateofbirth'])->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Fallback to dd-mm-yyyy format (for backward compatibility)
                $dateOfBirth = Carbon::createFromFormat('d-m-Y', $validated['dateofbirth'])->format('Y-m-d');
            } catch (\Exception $e2) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['dateofbirth' => 'Invalid date format.']);
            }
        }
        
        // Check email uniqueness (excluding current employee)
        $emailExists = Employee::where('emailid', $validated['emailid'])
            ->where('employeeid', '!=', $employee->employeeid)
            ->exists();
        
        // if ($emailExists) {
        //     return redirect()->back()
        //         ->withInput()
        //         ->withErrors(['emailid' => 'Email already exists.']);
        // }
        
        // Check username uniqueness (excluding current employee)
        $usernameExists = Employee::where('username', $validated['uname'])
            ->where('employeeid', '!=', $employee->employeeid)
            ->exists();
        
        if ($usernameExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['uname' => 'Username already exists.']);
        }
        
        $ip = $request->ip();
        
        // Handle profile photo upload
        if ($validated['upimg'] == 'Yes' && $request->hasFile('logo_img')) {
            // Delete old photo if exists
            if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            
            $file = $request->file('logo_img');
            $extension = $file->getClientOriginalExtension();
            $main = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = $main . date('YmdHis') . '.' . $extension;
            $employee->profile_photo = $file->storeAs('employees', $filename, 'public');
        }
        
        // Update employee fields (limited to what employee can edit)
        $employee->firstname = $validated['firstname'];
        $employee->lastname = $validated['lastname'];
        $employee->username = $validated['uname'];
        $employee->emailid = $validated['emailid'];
        $employee->phone = $validated['phone'];
        $employee->address1 = $validated['address'];
        $employee->address2 = $validated['address_formatted_address'] ?? $employee->address2;
        $employee->country = $validated['address_country'] ?? $employee->country ?? '';
        $employee->state = $validated['address_state'] ?? $employee->state ?? '';
        $employee->city = $validated['address_city'] ?? $employee->city ?? '';
        $employee->zipcode = $validated['address_zipcode'] ?? $employee->zipcode ?? '';
        $employee->dateofbirth = $dateOfBirth;
        $employee->editdate = now();
        $employee->editip = $ip;
        
        if ($employee->save()) {
            return redirect()->route('employee.profile.index')
                ->with('success', 'Profile Updated successfully');
        } else {
            return redirect()->route('employee.profile.edit')
                ->with('error', 'There occur some error in updating. Please Try Later');
        }
    }

    /**
     * AJAX: Check if email already exists.
     * Matches CI's dashboard/emailexistvalidate functionality.
     */
    public function checkEmailExists(Request $request)
    {
        $email = $request->input('email');
        $employee = Auth::guard('employee')->user();
        
        $exists = Employee::where('emailid', $email)
            ->where('employeeid', '!=', $employee->employeeid)
            ->exists();
        
        return response()->json(['exists' => $exists ? 1 : 0]);
    }

    /**
     * AJAX: Check if username already exists.
     * Matches CI's dashboard/usernameexist functionality.
     */
    public function checkUsernameExists(Request $request)
    {
        $username = $request->input('username');
        $employee = Auth::guard('employee')->user();
        
        $exists = Employee::where('username', $username)
            ->where('employeeid', '!=', $employee->employeeid)
            ->exists();
        
        return response()->json(['exists' => $exists ? 1 : 0]);
    }
}
