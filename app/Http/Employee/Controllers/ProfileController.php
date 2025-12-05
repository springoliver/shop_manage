<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Employee\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the employee's profile form.
     */
    public function edit(Request $request): View
    {
        return view('employee.profile.edit', [
            'user' => $request->user('employee'),
        ]);
    }

    /**
     * Update the employee's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user('employee');
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('employee.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the employee's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:employee'],
        ]);

        $user = $request->user('employee');

        Auth::guard('employee')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
