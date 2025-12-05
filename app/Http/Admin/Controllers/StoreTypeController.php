<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTypeRequest;
use App\Models\StoreType;
use App\Services\Admin\StoreTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreTypeController extends Controller
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
     *
     * @return View
     */
    public function index(): View
    {
        $perPage = request('per_page', 15);
        $storeTypes = $this->storeTypeService->getStoreTypes($perPage);

        return view('admin.store-types.index', compact('storeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.store-types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTypeRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->storeTypeService->saveStoreType($validated);

        return redirect()->route('admin.store-types.index')
            ->with('success', 'Store Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StoreType $storeType
     * @return View
     */
    public function edit(StoreType $storeType): View
    {
        return view('admin.store-types.edit', compact('storeType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreTypeRequest $request
     * @param StoreType $storeType
     * @return RedirectResponse
     */
    public function update(StoreTypeRequest $request, StoreType $storeType): RedirectResponse
    {
        $validated = $request->validated();

        $this->storeTypeService->updateStoreType($storeType, $validated);

        return redirect()->route('admin.store-types.index')
            ->with('success', 'Store Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StoreType $storeType
     * @return RedirectResponse
     */
    public function destroy(StoreType $storeType): RedirectResponse
    {
        $this->storeTypeService->deleteStoreType($storeType);

        return redirect()->route('admin.store-types.index')
            ->with('success', 'Store Category deleted successfully.');
    }

    /**
     * Change the status of the store type.
     *
     * @param \Illuminate\Http\Request $request
     * @param StoreType $storeType
     * @return RedirectResponse
     */
    public function changeStatus(\Illuminate\Http\Request $request, StoreType $storeType): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Enable,Disable',
        ]);

        $storeType->update(['status' => $validated['status']]);

        return redirect()->route('admin.store-types.index')
            ->with('success', 'Status changed successfully.');
    }
}

