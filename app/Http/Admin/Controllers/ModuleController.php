<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModuleRequest;
use App\Models\Module;
use App\Services\Admin\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * The module service instance.
     *
     * @var ModuleService
     */
    protected ModuleService $moduleService;

    /**
     * Create a new controller instance.
     *
     * @param ModuleService $moduleService
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $perPage = request('per_page', 15);
        $modules = $this->moduleService->getModules($perPage);

        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.modules.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ModuleRequest $request
     * @return RedirectResponse
     */
    public function store(ModuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->moduleService->saveModule($validated);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Module $module
     * @return View
     */
    public function edit(Module $module): View
    {
        return view('admin.modules.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ModuleRequest $request
     * @param Module $module
     * @return RedirectResponse
     */
    public function update(ModuleRequest $request, Module $module): RedirectResponse
    {
        $validated = $request->validated();

        $this->moduleService->updateModule($module, $validated);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Module $module
     * @return RedirectResponse
     */
    public function destroy(Module $module): RedirectResponse
    {
        $this->moduleService->deleteModule($module);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module deleted successfully.');
    }

    /**
     * Change the status of the module.
     *
     * @param \Illuminate\Http\Request $request
     * @param Module $module
     * @return RedirectResponse
     */
    public function changeStatus(\Illuminate\Http\Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Enable,Disable',
        ]);

        $module->update(['status' => $validated['status']]);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Status changed successfully.');
    }
}

