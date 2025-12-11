<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RequestedModule;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RequestModuleController extends Controller
{
    /**
     * Display a listing of the requested modules for the authenticated employee's store.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all requested modules for this employee's store
        $modules = DB::table('stoma_request_module')
            ->where('storeid', $employee->storeid)
            ->orderBy('rmid', 'DESC')
            ->get()
            ->map(function ($module) {
                return [
                    'rmid' => $module->rmid,
                    'storeid' => $module->storeid,
                    'module_name' => $module->subject ?? null,
                    'module_description' => $module->message ?? null,
                    'insertdate' => $module->insertdate,
                    'status' => $module->status,
                ];
            });
        
        return view('employee.requestmodule.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module request.
     */
    public function create(): View
    {
        return view('employee.requestmodule.create');
    }

    /**
     * Store a newly created module request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'modulename' => 'required|string|max:255',
            'moduledesc' => 'required|string',
        ], [
            'modulename.required' => 'Module name is required.',
            'moduledesc.required' => 'Please Enter the Module Description',
        ]);

        $employee = Auth::guard('employee')->user();
        
        // Check if module name already exists in stoma_module table
        $moduleExists = Module::where('module', $validated['modulename'])->exists();
        
        if ($moduleExists) {
            return redirect()->back()
                ->with('error', 'Module name already exists.')
                ->withInput();
        }
        
        // Strip HTML tags from description
        $moduleDescription = strip_tags($validated['moduledesc']);
        if (empty($moduleDescription)) {
            return redirect()->back()
                ->with('error', 'Please Enter the Module Description')
                ->withInput();
        }
        
        // Create new module request
        $moduleRequest = new RequestedModule();
        $moduleRequest->storeid = $employee->storeid;
        $moduleRequest->subject = $validated['modulename'];
        $moduleRequest->message = $moduleDescription;
        $moduleRequest->insertdate = now();
        $moduleRequest->insertip = $request->ip();
        $moduleRequest->insertby = 0; // Default value
        $moduleRequest->status = 'Pending';
        $moduleRequest->editdate = now(); // Current date
        $moduleRequest->editip = '';
        $moduleRequest->editby = 0; // Default value
        $moduleRequest->save();
        
        // TODO: Send email to admin (if email functionality is needed)
        
        return redirect()->route('employee.requestmodule.index')
            ->with('success', 'Your Request has been sent to Admin');
    }

    /**
     * Display the specified module request.
     */
    public function show(string $rmid): View
    {
        $rmid = base64_decode($rmid);
        $employee = Auth::guard('employee')->user();
        
        $module = DB::table('stoma_request_module')
            ->select('stoma_request_module.*', 's.store_name')
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'stoma_request_module.storeid')
            ->where('stoma_request_module.rmid', $rmid)
            ->where('stoma_request_module.storeid', $employee->storeid)
            ->first();
        
        if (!$module) {
            abort(404);
        }
        
        // Map subject to module_name and message to module_description for view compatibility
        $module->module_name = $module->subject;
        $module->module_description = $module->message;
        
        if (!$module) {
            abort(404);
        }
        
        return view('employee.requestmodule.view', compact('module'));
    }

    /**
     * Check if module name already exists (AJAX).
     */
    public function checkModuleName(Request $request)
    {
        $moduleName = $request->input('module_name');
        
        if (empty($moduleName)) {
            return response()->json(['valid' => true]);
        }
        
        $moduleExists = Module::where('module', $moduleName)->exists();
        
        return response()->json(['valid' => !$moduleExists]);
    }
}

