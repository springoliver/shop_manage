<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resignation;
use App\Models\StoreEmployee;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResignationController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Resignation module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Resignation')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of resignations.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $resignations = Resignation::with('employee')
            ->where('storeid', $storeid)
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Deactivate');
            })
            ->orderBy('resignationid', 'DESC')
            ->get();
        
        return view('storeowner.resignation.index', compact('resignations'));
    }

    /**
     * Get resignations by type (pending).
     */
    public function getResignationByType(string $type): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $query = Resignation::with('employee')
            ->where('storeid', $storeid)
            ->whereHas('employee', function ($q) {
                $q->where('status', '!=', 'Deactivate');
            });
        
        if ($type === 'pending') {
            $query->where('status', 'Pending');
        }
        
        $resignations = $query->orderBy('resignationid', 'DESC')->get();
        
        return view('storeowner.resignation.index', compact('resignations'));
    }

    /**
     * Change the status of a resignation.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'resignationid' => 'required|string',
            'status' => 'required|string|in:Pending,Declined,Approved',
        ]);
        
        $resignationid = base64_decode($validated['resignationid']);
        $resignation = Resignation::findOrFail($resignationid);
        
        $resignation->update([
            'status' => $validated['status'],
            'editdatetime' => now(),
            'editip' => $request->ip(),
        ]);
        
        return redirect()->route('storeowner.resignation.index')
            ->with('success', 'Resignation Request Status changed Successfully.');
    }

    /**
     * Display the specified resignation.
     */
    public function view(string $resignationid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $resignationid = base64_decode($resignationid);
        $resignation = Resignation::with('employee')
            ->findOrFail($resignationid);
        
        return view('storeowner.resignation.view', compact('resignation'));
    }

    /**
     * Remove the specified resignation.
     */
    public function destroy(string $resignationid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $resignationid = base64_decode($resignationid);
        $resignation = Resignation::findOrFail($resignationid);
        $resignation->delete();
        
        return redirect()->route('storeowner.resignation.index')
            ->with('success', 'Resignation has been deleted successfully');
    }

    /**
     * Search resignations.
     */
    public function search(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $search = $request->input('search', '');
        if (preg_match('/\s/', $search)) {
            $searchParts = explode(' ', $search);
            $search = $searchParts[0];
        }
        
        $storeid = $this->getStoreId();
        
        $resignations = Resignation::with('employee')
            ->where('storeid', $storeid)
            ->where(function ($query) use ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', "%{$search}%")
                      ->orWhere('lastname', 'LIKE', "%{$search}%");
                })
                ->orWhere('from_date', 'LIKE', "%{$search}%")
                ->orWhere('subject', 'LIKE', "%{$search}%");
            })
            ->orderBy('resignationid', 'DESC')
            ->get();
        
        return view('storeowner.resignation.index', compact('resignations', 'search'));
    }
}

