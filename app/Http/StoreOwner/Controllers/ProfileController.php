<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Http\StoreOwner\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the store owner's profile form.
     */
    public function edit(Request $request): View
    {
        return view('storeowner.profile.edit', [
            'user' => $request->user('storeowner'),
        ]);
    }

    /**
     * Update the store owner's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user('storeowner');
        $user->fill($request->validated());

        if ($user->isDirty('emailid')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('storeowner.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the store owner's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:storeowner'],
        ]);

        $user = $request->user('storeowner');

        Auth::guard('storeowner')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

