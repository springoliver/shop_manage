<?php

namespace App\Http\StoreOwner\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StoreOwner;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\UserGroup;
use App\Models\Department;
use App\Models\Module;
use App\Models\PaidModule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        $userGroups = UserGroup::where('storeid', 0)->orderBy('groupname')->get();
        
        return view('storeowner.auth.register', compact('storeTypes', 'userGroups'));
    }

    /**
     * Handle an incoming registration request for store owner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'emailid' => ['required', 'string', 'email', 'max:255', 'unique:stoma_storeowner,emailid'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'dateofbirth' => ['required', 'date'],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address1' => ['required', 'string', 'max:255'],
            'address_lat' => ['nullable', 'string'],
            'address_lng' => ['nullable', 'string'],
            'address_formatted_address' => ['nullable', 'string'],
            'address_state' => ['nullable', 'string'],
            'address_city' => ['nullable', 'string'],
            'address_zipcode' => ['nullable', 'string'],
            'accept_terms' => ['required', 'accepted'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Hash the password using Laravel's Hash
        $validated['password'] = Hash::make($validated['password']);

        // Map address fields from Google Places API
        if ($request->filled('address_city')) {
            $validated['city'] = $request->input('address_city');
        }
        if ($request->filled('address_state')) {
            $validated['state'] = $request->input('address_state');
        }
        if ($request->filled('address_zipcode')) {
            $validated['zipcode'] = $request->input('address_zipcode');
        }
        if ($request->filled('address_formatted_address')) {
            $validated['address2'] = $request->input('address_formatted_address');
        }

        // Set signup metadata
        $validated['signupdate'] = now();
        $validated['signupip'] = $request->ip();
        $validated['signupby'] = 0;
        $validated['editdate'] = now();
        $validated['editip'] = $request->ip();
        $validated['editby'] = 0;
        $validated['status'] = 'Pending Setup';
        $validated['accept_terms'] = 'Yes';

        $storeOwner = StoreOwner::create($validated);

        // TODO: Send activation email here
        // Redirect to store registration page
        return redirect()->route('storeowner.register.store', ['ownerid' => $storeOwner->ownerid])
            ->with('success', 'Owner registered successfully. Please complete store registration.');
    }

    /**
     * Display the store registration view.
     */
    public function createStore(Request $request): View
    {
        $ownerid = $request->query('ownerid');
        
        if (!$ownerid || !StoreOwner::where('ownerid', $ownerid)->exists()) {
            abort(404, 'Owner not found');
        }

        $storeTypes = StoreType::where('status', 'Enable')->orderBy('store_type')->get();
        
        return view('storeowner.auth.register-store', compact('ownerid', 'storeTypes'));
    }

    /**
     * Handle store registration (step 2).
     */
    public function storeRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ownerid' => ['required', 'exists:stoma_storeowner,ownerid'],
            'store_name' => ['required', 'string', 'max:255', 'unique:stoma_store,store_name'],
            'typeid' => ['required', 'exists:stoma_storetype,typeid'],
            'address1' => ['required', 'string', 'max:255'],
            'address_lat1' => ['nullable', 'string'],
            'address_lng1' => ['nullable', 'string'],
            'address_formatted_address1' => ['nullable', 'string'],
            'address_street_number1' => ['nullable', 'string'],
            'address_street1' => ['nullable', 'string'],
            'address_state1' => ['nullable', 'string'],
            'address_country1' => ['nullable', 'string'],
            'address_city1' => ['nullable', 'string'],
            'address_zipcode1' => ['nullable', 'string'],
            'address_location_type1' => ['nullable', 'string'],
            'weburl' => ['required', 'url', 'max:255'],
            'store_email' => ['required', 'email', 'max:255'],
            'logo_img' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle logo upload
        $logofile = null;
        if ($request->hasFile('logo_img')) {
            $logofile = $request->file('logo_img')->store('store-logos', 'public');
        }

        // Prepare store data
        $storeData = [
            'storeownerid' => $validated['ownerid'],
            'store_name' => $validated['store_name'],
            'typeid' => $validated['typeid'],
            'full_google_address' => $validated['address1'],
            'latitude' => $validated['address_lat1'] ?? null,
            'longitude' => $validated['address_lng1'] ?? null,
            'website_url' => $validated['weburl'],
            'store_email' => $validated['store_email'],
            'logofile' => $logofile,
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
        // Get departments by storetypeid (matching CI logic)
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

        // TODO: Copy sale_target data if needed (skipping for now)
        
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
        
        // TODO: Send notification email to admin

        return redirect()->route('storeowner.login')
            ->with('success', 'You are registered successfully. You can login your account after activation.');
    }
}

