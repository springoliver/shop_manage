<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        return view('storeowner.dashboard');
    }
}

