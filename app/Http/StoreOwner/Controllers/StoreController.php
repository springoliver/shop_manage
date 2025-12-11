<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\UserGroup;
use App\Models\Department;
use App\Models\Module;
use App\Models\PaidModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Carbon\Carbon;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores.
     */
    public function index(Request $request): View
    {
        $user = auth('storeowner')->user();
        $ownerid = $user->ownerid;
        
        // Load all stores for client-side pagination and sorting
        $stores = Store::with('storeType')
            ->where('storeownerid', $ownerid)
            ->orderBy('insertdate', 'desc')
            ->get();
        
        return view('storeowner.store.index', compact('stores'));
    }

    /**
     * Show the form for creating a new store.
     */
    public function create(): View
    {
        $storeid = session('storeid', 0);
        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        $userGroups = UserGroup::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })->orderBy('groupname')->get();
        
        return view('storeowner.store.create', compact('storeTypes', 'userGroups'));
    }

    /**
     * Show the form for editing a store.
     */
    public function edit(Store $store): View
    {
        $user = auth('storeowner')->user();
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }
        
        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        $userGroups = UserGroup::where(function($query) use ($store) {
            $query->where('storeid', $store->storeid)
                  ->orWhere('storeid', 0);
        })->orderBy('groupname')->get();
        
        // Get selected user groups for this store
        // TODO: Implement store_usergroup relationship if needed
        
        return view('storeowner.store.edit', compact('store', 'storeTypes', 'userGroups'));
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store): View
    {
        $user = auth('storeowner')->user();
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('storeowner.store.view', compact('store'));
    }

    /**
     * Store a newly created store.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth('storeowner')->user();
        
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255', 'unique:stoma_store,store_name'],
            'typeid' => ['required', 'exists:stoma_storetype,typeid'],
            'weburl' => ['required', 'url', 'max:255'],
            'store_email' => ['required', 'email', 'max:255', 'unique:stoma_store,store_email'],
            'store_email_pass' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['required', 'email', 'max:255'],
            'address1' => ['required', 'string', 'max:255'],
            'address_lat' => ['nullable', 'string'],
            'address_lng' => ['nullable', 'string'],
            'logo_img' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'groupname' => ['nullable', 'array'],
            'groupname.*' => ['exists:stoma_usergroup,usergroupid'],
            // Opening hours
            'monday_hour_from' => ['nullable', 'string'],
            'monday_hour_to' => ['nullable', 'string'],
            'monday_dayoff' => ['nullable', 'in:Yes,No'],
            'tuesday_hour_from' => ['nullable', 'string'],
            'tuesday_hour_to' => ['nullable', 'string'],
            'tuesday_dayoff' => ['nullable', 'in:Yes,No'],
            'wednesday_hour_from' => ['nullable', 'string'],
            'wednesday_hour_to' => ['nullable', 'string'],
            'wednesday_dayoff' => ['nullable', 'in:Yes,No'],
            'thursday_hour_from' => ['nullable', 'string'],
            'thursday_hour_to' => ['nullable', 'string'],
            'thursday_dayoff' => ['nullable', 'in:Yes,No'],
            'friday_hour_from' => ['nullable', 'string'],
            'friday_hour_to' => ['nullable', 'string'],
            'friday_dayoff' => ['nullable', 'in:Yes,No'],
            'saturday_hour_from' => ['nullable', 'string'],
            'saturday_hour_to' => ['nullable', 'string'],
            'saturday_dayoff' => ['nullable', 'in:Yes,No'],
            'sunday_hour_from' => ['nullable', 'string'],
            'sunday_hour_to' => ['nullable', 'string'],
            'sunday_dayoff' => ['nullable', 'in:Yes,No'],
        ]);

        // Handle logo upload
        $logofile = null;
        if ($request->hasFile('logo_img')) {
            $logofile = $request->file('logo_img')->store('store-logos', 'public');
        }

        // Prepare store data
        $storeData = [
            'storeownerid' => $user->ownerid,
            'store_name' => $validated['store_name'],
            'typeid' => $validated['typeid'],
            'full_google_address' => $validated['address1'],
            'latitude' => $validated['address_lat'] ?? null,
            'longitude' => $validated['address_lng'] ?? null,
            'website_url' => $validated['weburl'],
            'store_email' => $validated['store_email'],
            'store_email_pass' => $validated['store_email_pass'] ?? null,
            'manager_email' => $validated['manager_email'],
            'logofile' => $logofile,
            'monday_hour_from' => $validated['monday_hour_from'] ?? null,
            'monday_hour_to' => $validated['monday_hour_to'] ?? null,
            'monday_dayoff' => $validated['monday_dayoff'] ?? 'No',
            'tuesday_hour_from' => $validated['tuesday_hour_from'] ?? null,
            'tuesday_hour_to' => $validated['tuesday_hour_to'] ?? null,
            'tuesday_dayoff' => $validated['tuesday_dayoff'] ?? 'No',
            'wednesday_hour_from' => $validated['wednesday_hour_from'] ?? null,
            'wednesday_hour_to' => $validated['wednesday_hour_to'] ?? null,
            'wednesday_dayoff' => $validated['wednesday_dayoff'] ?? 'No',
            'thursday_hour_from' => $validated['thursday_hour_from'] ?? null,
            'thursday_hour_to' => $validated['thursday_hour_to'] ?? null,
            'thursday_dayoff' => $validated['thursday_dayoff'] ?? 'No',
            'friday_hour_from' => $validated['friday_hour_from'] ?? null,
            'friday_hour_to' => $validated['friday_hour_to'] ?? null,
            'friday_dayoff' => $validated['friday_dayoff'] ?? 'No',
            'saturday_hour_from' => $validated['saturday_hour_from'] ?? null,
            'saturday_hour_to' => $validated['saturday_hour_to'] ?? null,
            'saturday_dayoff' => $validated['saturday_dayoff'] ?? 'No',
            'sunday_hour_from' => $validated['sunday_hour_from'] ?? null,
            'sunday_hour_to' => $validated['sunday_hour_to'] ?? null,
            'sunday_dayoff' => $validated['sunday_dayoff'] ?? 'No',
            'insertdate' => now(),
            'insertip' => $request->ip(),
            'insertby' => 0,
            'editdate' => now(),
            'editip' => $request->ip(),
            'editby' => 0,
            'status' => 'Pending Setup',
        ];

        $store = Store::create($storeData);
        $storeid = $store->storeid;

        // Copy departments from store type to store
        $departments = Department::where('storetypeid', $validated['typeid'])
            ->where('status', 'Enable')
            ->get();

        foreach ($departments as $department) {
            Department::create([
                'department' => $department->department,
                'storetypeid' => $department->storetypeid,
                'storeid' => $storeid,
                'roster_max_time' => $department->roster_max_time,
                'status' => $department->status,
            ]);
        }

        // TODO: Handle user groups assignment (store_usergroup table)
        // TODO: Handle module access creation
        // TODO: Copy sales_target data
        
        // Create paid_module entries for modules with free_days > 0
        // Only create if they don't already exist for this store
        $modules = Module::where('status', 'Enable')->get();
        if ($modules->count() > 0) {
            foreach ($modules as $module) {
                if ($module->free_days > 0) {
                    // Check if paid_module entry already exists for this store and module
                    $existingPaidModule = PaidModule::where('storeid', $storeid)
                        ->where('moduleid', $module->moduleid)
                        ->first();
                    
                    if (!$existingPaidModule) {
                        $purchaseDate = Carbon::now()->startOfDay();
                        $expireDate = Carbon::now()->addDays($module->free_days)->endOfDay();
                        
                        PaidModule::create([
                            'storeid' => $storeid,
                            'moduleid' => $module->moduleid,
                            'purchase_date' => $purchaseDate,
                            'expire_date' => $expireDate,
                            'paid_amount' => '0.00',
                            'status' => 'Enable',
                            'insertdatetime' => now(),
                            'insertip' => $request->ip(),
                            'isTrial' => 1,
                        ]);
                    }
                }
            }
        }
        
        // TODO: Send email notification

        return redirect()->route('storeowner.store.index')
            ->with('success', 'Store created successfully.');
    }

    /**
     * Update store information (simpler update without logo).
     */
    public function updateStoreInfo(Request $request): RedirectResponse
    {
        $user = auth('storeowner')->user();
        
        $validated = $request->validate([
            'storeid' => ['required', 'exists:stoma_store,storeid'],
            'store_name' => ['required', 'string', 'max:255'],
            'typeid' => ['required', 'exists:stoma_storetype,typeid'],
            'weburl' => ['required', 'url', 'max:255'],
            'store_email' => ['required', 'email', 'max:255'],
            'store_email_pass' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['required', 'email', 'max:255'],
            'address1' => ['required', 'string', 'max:255'],
            'address_lat' => ['nullable', 'string'],
            'address_lng' => ['nullable', 'string'],
            'logo_img' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            // Opening hours
            'monday_hour_from' => ['nullable', 'string'],
            'monday_hour_to' => ['nullable', 'string'],
            'monday_dayoff' => ['nullable', 'in:Yes,No'],
            'tuesday_hour_from' => ['nullable', 'string'],
            'tuesday_hour_to' => ['nullable', 'string'],
            'tuesday_dayoff' => ['nullable', 'in:Yes,No'],
            'wednesday_hour_from' => ['nullable', 'string'],
            'wednesday_hour_to' => ['nullable', 'string'],
            'wednesday_dayoff' => ['nullable', 'in:Yes,No'],
            'thursday_hour_from' => ['nullable', 'string'],
            'thursday_hour_to' => ['nullable', 'string'],
            'thursday_dayoff' => ['nullable', 'in:Yes,No'],
            'friday_hour_from' => ['nullable', 'string'],
            'friday_hour_to' => ['nullable', 'string'],
            'friday_dayoff' => ['nullable', 'in:Yes,No'],
            'saturday_hour_from' => ['nullable', 'string'],
            'saturday_hour_to' => ['nullable', 'string'],
            'saturday_dayoff' => ['nullable', 'in:Yes,No'],
            'sunday_hour_from' => ['nullable', 'string'],
            'sunday_hour_to' => ['nullable', 'string'],
            'sunday_dayoff' => ['nullable', 'in:Yes,No'],
        ]);

        $store = Store::findOrFail($validated['storeid']);
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }

        // Handle logo upload if provided
        if ($request->hasFile('logo_img')) {
            // Delete old logo if exists
            if ($store->logofile) {
                Storage::disk('public')->delete($store->logofile);
            }
            $validated['logofile'] = $request->file('logo_img')->store('store-logos', 'public');
        }

        // Prepare update data
        $updateData = [
            'store_name' => $validated['store_name'],
            'typeid' => $validated['typeid'],
            'full_google_address' => $validated['address1'],
            'latitude' => $validated['address_lat'] ?? null,
            'longitude' => $validated['address_lng'] ?? null,
            'website_url' => $validated['weburl'],
            'store_email' => $validated['store_email'],
            'store_email_pass' => $validated['store_email_pass'] ?? null,
            'manager_email' => $validated['manager_email'],
            'monday_hour_from' => $validated['monday_hour_from'] ?? null,
            'monday_hour_to' => $validated['monday_hour_to'] ?? null,
            'monday_dayoff' => $validated['monday_dayoff'] ?? 'No',
            'tuesday_hour_from' => $validated['tuesday_hour_from'] ?? null,
            'tuesday_hour_to' => $validated['tuesday_hour_to'] ?? null,
            'tuesday_dayoff' => $validated['tuesday_dayoff'] ?? 'No',
            'wednesday_hour_from' => $validated['wednesday_hour_from'] ?? null,
            'wednesday_hour_to' => $validated['wednesday_hour_to'] ?? null,
            'wednesday_dayoff' => $validated['wednesday_dayoff'] ?? 'No',
            'thursday_hour_from' => $validated['thursday_hour_from'] ?? null,
            'thursday_hour_to' => $validated['thursday_hour_to'] ?? null,
            'thursday_dayoff' => $validated['thursday_dayoff'] ?? 'No',
            'friday_hour_from' => $validated['friday_hour_from'] ?? null,
            'friday_hour_to' => $validated['friday_hour_to'] ?? null,
            'friday_dayoff' => $validated['friday_dayoff'] ?? 'No',
            'saturday_hour_from' => $validated['saturday_hour_from'] ?? null,
            'saturday_hour_to' => $validated['saturday_hour_to'] ?? null,
            'saturday_dayoff' => $validated['saturday_dayoff'] ?? 'No',
            'sunday_hour_from' => $validated['sunday_hour_from'] ?? null,
            'sunday_hour_to' => $validated['sunday_hour_to'] ?? null,
            'sunday_dayoff' => $validated['sunday_dayoff'] ?? 'No',
            'editdate' => now(),
            'editip' => $request->ip(),
            'editby' => 0,
        ];

        if (isset($validated['logofile'])) {
            $updateData['logofile'] = $validated['logofile'];
        }

        $store->update($updateData);

        return redirect()->route('storeowner.store.index')
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Update the specified store (full update with logo).
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        $user = auth('storeowner')->user();
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255', 'unique:stoma_store,store_name,' . $store->storeid . ',storeid'],
            'typeid' => ['required', 'exists:stoma_storetype,typeid'],
            'weburl' => ['required', 'url', 'max:255'],
            'store_email' => ['required', 'email', 'max:255', 'unique:stoma_store,store_email,' . $store->storeid . ',storeid'],
            'store_email_pass' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['required', 'email', 'max:255'],
            'address1' => ['required', 'string', 'max:255'],
            'address_lat' => ['nullable', 'string'],
            'address_lng' => ['nullable', 'string'],
            'logo_img' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'groupname' => ['nullable', 'array'],
            'groupname.*' => ['exists:stoma_usergroup,usergroupid'],
            // Opening hours
            'monday_hour_from' => ['nullable', 'string'],
            'monday_hour_to' => ['nullable', 'string'],
            'monday_dayoff' => ['nullable', 'in:Yes,No'],
            'tuesday_hour_from' => ['nullable', 'string'],
            'tuesday_hour_to' => ['nullable', 'string'],
            'tuesday_dayoff' => ['nullable', 'in:Yes,No'],
            'wednesday_hour_from' => ['nullable', 'string'],
            'wednesday_hour_to' => ['nullable', 'string'],
            'wednesday_dayoff' => ['nullable', 'in:Yes,No'],
            'thursday_hour_from' => ['nullable', 'string'],
            'thursday_hour_to' => ['nullable', 'string'],
            'thursday_dayoff' => ['nullable', 'in:Yes,No'],
            'friday_hour_from' => ['nullable', 'string'],
            'friday_hour_to' => ['nullable', 'string'],
            'friday_dayoff' => ['nullable', 'in:Yes,No'],
            'saturday_hour_from' => ['nullable', 'string'],
            'saturday_hour_to' => ['nullable', 'string'],
            'saturday_dayoff' => ['nullable', 'in:Yes,No'],
            'sunday_hour_from' => ['nullable', 'string'],
            'sunday_hour_to' => ['nullable', 'string'],
            'sunday_dayoff' => ['nullable', 'in:Yes,No'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo_img')) {
            // Delete old logo if exists
            if ($store->logofile) {
                Storage::disk('public')->delete($store->logofile);
            }
            $validated['logofile'] = $request->file('logo_img')->store('store-logos', 'public');
        }

        // Prepare update data
        $updateData = [
            'store_name' => $validated['store_name'],
            'typeid' => $validated['typeid'],
            'full_google_address' => $validated['address1'],
            'latitude' => $validated['address_lat'] ?? null,
            'longitude' => $validated['address_lng'] ?? null,
            'website_url' => $validated['weburl'],
            'store_email' => $validated['store_email'],
            'store_email_pass' => $validated['store_email_pass'] ?? null,
            'manager_email' => $validated['manager_email'],
            'monday_hour_from' => $validated['monday_hour_from'] ?? null,
            'monday_hour_to' => $validated['monday_hour_to'] ?? null,
            'monday_dayoff' => $validated['monday_dayoff'] ?? 'No',
            'tuesday_hour_from' => $validated['tuesday_hour_from'] ?? null,
            'tuesday_hour_to' => $validated['tuesday_hour_to'] ?? null,
            'tuesday_dayoff' => $validated['tuesday_dayoff'] ?? 'No',
            'wednesday_hour_from' => $validated['wednesday_hour_from'] ?? null,
            'wednesday_hour_to' => $validated['wednesday_hour_to'] ?? null,
            'wednesday_dayoff' => $validated['wednesday_dayoff'] ?? 'No',
            'thursday_hour_from' => $validated['thursday_hour_from'] ?? null,
            'thursday_hour_to' => $validated['thursday_hour_to'] ?? null,
            'thursday_dayoff' => $validated['thursday_dayoff'] ?? 'No',
            'friday_hour_from' => $validated['friday_hour_from'] ?? null,
            'friday_hour_to' => $validated['friday_hour_to'] ?? null,
            'friday_dayoff' => $validated['friday_dayoff'] ?? 'No',
            'saturday_hour_from' => $validated['saturday_hour_from'] ?? null,
            'saturday_hour_to' => $validated['saturday_hour_to'] ?? null,
            'saturday_dayoff' => $validated['saturday_dayoff'] ?? 'No',
            'sunday_hour_from' => $validated['sunday_hour_from'] ?? null,
            'sunday_hour_to' => $validated['sunday_hour_to'] ?? null,
            'sunday_dayoff' => $validated['sunday_dayoff'] ?? 'No',
            'editdate' => now(),
            'editip' => $request->ip(),
            'editby' => 0,
        ];

        if (isset($validated['logofile'])) {
            $updateData['logofile'] = $validated['logofile'];
        }

        $store->update($updateData);

        // TODO: Handle user groups update (store_usergroup table)
        // TODO: Handle module access update
        // TODO: Update sales_target data

        return redirect()->route('storeowner.store.index')
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Remove the specified store.
     */
    public function destroy(Store $store): RedirectResponse
    {
        $user = auth('storeowner')->user();
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }

        // Delete logo if exists
        if ($store->logofile) {
            Storage::disk('public')->delete($store->logofile);
        }

        $store->delete();

        return redirect()->route('storeowner.store.index')
            ->with('success', 'Store deleted successfully.');
    }

    /**
     * Change store status.
     */
    public function changeStatus(Request $request, Store $store): RedirectResponse
    {
        $user = auth('storeowner')->user();
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Active,Closed,Pending Setup,Suspended'],
        ]);

        $store->update(['status' => $validated['status']]);

        return redirect()->route('storeowner.store.index')
            ->with('success', 'Store status changed successfully.');
    }

    /**
     * Check if store name exists (AJAX).
     */
    public function checkStoreName(Request $request)
    {
        $storeName = $request->input('store_name');
        $exists = Store::where('store_name', $storeName)->exists();
        
        return response()->json([
            'message' => $exists ? 'invalid' : 'valid'
        ]);
    }

    /**
     * Check if store email exists (AJAX).
     */
    public function checkStoreEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = Store::where('store_email', $email)->exists();
        
        return response($exists ? 'invalid' : 'valid');
    }

    /**
     * Change the active store.
     */
    public function changeStore()
    {
        $user = auth('storeowner')->user();
        
        request()->validate([
            'storeid' => ['required', 'exists:stoma_store,storeid']
        ]);

        $store = Store::findOrFail(request('storeid'));
        
        // Ensure the store belongs to the authenticated owner
        if ($store->storeownerid != $user->ownerid) {
            abort(403, 'Unauthorized access.');
        }

        session(['storeid' => request('storeid')]);

        return redirect()->back()->with('success', 'Store changed successfully.');
    }
}

