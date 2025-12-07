<?php

namespace App\Http\StoreOwner\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StoreOwner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    /**
     * Activate store owner account.
     *
     * @param Request $request
     * @param string $token
     * @return RedirectResponse
     */
    public function activate(string $token): RedirectResponse
    {
        try {
            $ownerid = base64_decode($token);
            
            if (!$ownerid || !is_numeric($ownerid)) {
                return redirect()->route('storeowner.login')
                    ->with('error', 'Invalid activation link.');
            }

            $storeOwner = StoreOwner::find($ownerid);

            if (!$storeOwner) {
                return redirect()->route('storeowner.login')
                    ->with('error', 'Invalid activation link.');
            }

            // Check if already activated
            if ($storeOwner->status === 'Active') {
                return redirect()->route('storeowner.login')
                    ->with('error', 'Your account is already activated. You can login to your account with your email and password.');
            }

            // Activate the account
            $storeOwner->update([
                'status' => 'Active',
                'editdate' => now(),
                'editip' => request()->ip(),
            ]);

            return redirect()->route('storeowner.login')
                ->with('success', 'Your account is activated successfully. You can login to your account with your email and password.');
        } catch (\Exception $e) {
            return redirect()->route('storeowner.login')
                ->with('error', 'There occurred some error in activating your account!');
        }
    }
}

