<?php

namespace App\Http\Employee\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user('employee')->hasVerifiedEmail()) {
            return redirect()->intended(route('employee.dashboard', absolute: false));
        }

        $request->user('employee')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
