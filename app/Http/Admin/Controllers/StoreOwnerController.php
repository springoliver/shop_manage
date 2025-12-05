<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOwnerRequest;
use App\Models\StoreOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StoreOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $storeOwners = StoreOwner::orderBy('signupdate', 'desc')->paginate(15);

        return view('admin.store-owners.index', compact('storeOwners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.store-owners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOwnerRequest $request)
    {
        $validated = $request->validated();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Hash the password
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Set signup metadata
        $validated['signupdate'] = now();
        $validated['signupip'] = $request->ip();
        $validated['signupby'] = auth('admin')->id() ?? 0;
        $validated['editdate'] = now();
        $validated['editip'] = $request->ip();
        $validated['editby'] = auth('admin')->id() ?? 0;

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'Pending Setup';
        $validated['accept_terms'] = $validated['accept_terms'] ?? 'Yes';

        StoreOwner::create($validated);

        return redirect()->route('admin.store-owners.index')
            ->with('success', 'Store owner created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreOwner $storeOwner)
    {
        return view('admin.store-owners.show', compact('storeOwner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StoreOwner $storeOwner)
    {
        return view('admin.store-owners.edit', compact('storeOwner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOwnerRequest $request, StoreOwner $storeOwner)
    {
        $validated = $request->validated();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($storeOwner->profile_photo) {
                Storage::disk('public')->delete($storeOwner->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Hash the password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Set edit metadata
        $validated['editdate'] = now();
        $validated['editip'] = $request->ip();
        $validated['editby'] = auth('admin')->id() ?? 0;

        $storeOwner->update($validated);

        return redirect()->route('admin.store-owners.index')
            ->with('success', 'Store owner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreOwner $storeOwner)
    {
        // Delete profile photo if exists
        if ($storeOwner->profile_photo) {
            Storage::disk('public')->delete($storeOwner->profile_photo);
        }

        $storeOwner->delete();

        return redirect()->route('admin.store-owners.index')
            ->with('success', 'Store owner deleted successfully.');
    }

    /**
     * Change the status of the store owner.
     *
     * @param Request $request
     * @param StoreOwner $storeOwner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(Request $request, StoreOwner $storeOwner)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending Setup,Active,Suspended,Closed',
        ]);

        $storeOwner->update(['status' => $validated['status']]);

        return redirect()->route('admin.store-owners.index')
            ->with('success', 'Status changed successfully.');
    }
}

