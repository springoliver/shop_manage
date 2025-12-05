<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestedModule;
use App\Services\Admin\RequestedModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestedModuleController extends Controller
{
    /**
     * The requested module service instance.
     *
     * @var RequestedModuleService
     */
    protected RequestedModuleService $requestedModuleService;

    /**
     * Create a new controller instance.
     *
     * @param RequestedModuleService $requestedModuleService
     */
    public function __construct(RequestedModuleService $requestedModuleService)
    {
        $this->requestedModuleService = $requestedModuleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $perPage = request('per_page', 15);
        $requestedModules = $this->requestedModuleService->getRequestedModules($perPage);

        return view('admin.requested-modules.index', compact('requestedModules'));
    }

    /**
     * Change the status of the requested module.
     *
     * @param Request $request
     * @param RequestedModule $requestedModule
     * @return RedirectResponse
     */
    public function changeStatus(Request $request, RequestedModule $requestedModule): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Seen',
        ]);

        $this->requestedModuleService->updateStatus($requestedModule, $validated['status']);

        // If status changed to "Seen", send email notification
        if ($validated['status'] === 'Seen') {
            $moduleDetails = $this->requestedModuleService->getRequestedModuleById($requestedModule->rmid);
            
            if ($moduleDetails && $moduleDetails->store_email) {
                // TODO: Implement email sending logic similar to CI
                // This would require email template configuration
                // For now, we'll just update the status
            }
        }

        return redirect()->route('admin.requested-modules.index')
            ->with('success', 'Status changed successfully.');
    }
}
