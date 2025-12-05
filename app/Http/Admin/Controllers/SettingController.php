<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * The setting service instance.
     *
     * @var SettingService
     */
    protected SettingService $settingService;

    /**
     * Create a new controller instance.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $settings = $this->settingService->getSettings(10);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Setting $setting
     * @return View
     */
    public function edit(Setting $setting): View
    {
        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingRequest $request
     * @param Setting $setting
     * @return RedirectResponse
     */
    public function update(SettingRequest $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validated();

        $this->settingService->updateSetting($setting, $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }
}

