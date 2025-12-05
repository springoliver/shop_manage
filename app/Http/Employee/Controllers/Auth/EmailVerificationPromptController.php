<?php

namespace App\Http\Employee\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user('employee')->hasVerifiedEmail()
                    ? redirect()->intended(route('employee.dashboard', absolute: false))
                    : view('employee.auth.verify-email');
    }
}
