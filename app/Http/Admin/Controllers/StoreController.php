<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequest;
use App\Models\Store;
use App\Models\StoreOwner;
use App\Services\Admin\StoreTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * The store type service instance.
     *
     * @var StoreTypeService
     */
    protected StoreTypeService $storeTypeService;

    /**
     * Create a new controller instance.
     *
     * @param StoreTypeService $storeTypeService
     */
    public function __construct(StoreTypeService $storeTypeService)
    {
        $this->storeTypeService = $storeTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::with('storeOwner')
            ->orderBy('insertdate', 'desc')
            ->paginate(15);

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $storeOwners = StoreOwner::orderBy('firstname')
            ->get();

        $storeTypes = $this->storeTypeService->getEnabledStoreTypes();

        return view('admin.stores.create', compact('storeOwners', 'storeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logofile')) {
            $validated['logofile'] = $request->file('logofile')->store('store-logos', 'public');
        }

        // Set insert metadata
        $validated['insertdate'] = now();
        $validated['insertip'] = $request->ip();
        $validated['insertby'] = auth('admin')->id() ?? 0;
        $validated['editdate'] = now();
        $validated['editip'] = $request->ip();
        $validated['editby'] = auth('admin')->id() ?? 0;

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'Active';

        Store::create($validated);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        $store->load('storeOwner');

        return view('admin.stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        $storeOwners = StoreOwner::orderBy('firstname')
            ->get();

        $storeTypes = $this->storeTypeService->getEnabledStoreTypes();

        return view('admin.stores.edit', compact('store', 'storeOwners', 'storeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, Store $store)
    {
        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logofile')) {
            // Delete old logo if exists
            if ($store->logofile) {
                Storage::disk('public')->delete($store->logofile);
            }
            $validated['logofile'] = $request->file('logofile')->store('store-logos', 'public');
        } elseif ($request->input('remove_logo') === 'yes') {
            // Remove logo if requested
            if ($store->logofile) {
                Storage::disk('public')->delete($store->logofile);
            }
            $validated['logofile'] = null;
        }

        // Set edit metadata
        $validated['editdate'] = now();
        $validated['editip'] = $request->ip();
        $validated['editby'] = auth('admin')->id() ?? 0;

        $store->update($validated);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        // Delete logo if exists
        if ($store->logofile) {
            Storage::disk('public')->delete($store->logofile);
        }

        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    /**
     * Change the status of the store.
     *
     * @param Request $request
     * @param Store $store
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(Request $request, Store $store)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending Setup,Active,Suspended,Closed',
        ]);

        $store->update(['status' => $validated['status']]);

        return redirect()->route('admin.stores.index')
            ->with('success', 'Status changed successfully.');
    }
}

