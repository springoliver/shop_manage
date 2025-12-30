<?php

namespace App\Http\StoreOwner\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StoreOwner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('storeowner.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'emailid' => ['required', 'email'],
        ]);

        // Find the user by emailid since we use custom column name
        $storeOwner = StoreOwner::where('emailid', $request->emailid)->first();

        if (!$storeOwner) {
            // Return error if user not found (Laravel's standard behavior)
            return back()->withInput($request->only('emailid'))
                ->withErrors(['emailid' => __('passwords.user')]);
        }

        // Check if user is active
        if ($storeOwner->status !== 'Active') {
            return back()->withInput($request->only('emailid'))
                ->withErrors(['emailid' => 'Your account is not active. Please activate your account first.']);
        }

        // Use the password broker to send reset link
        // The custom user provider will handle emailid column mapping
        $status = Password::broker('storeowners')->sendResetLink(
            ['email' => $request->emailid]
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('emailid'))
                        ->withErrors(['emailid' => __($status)]);
    }
}

