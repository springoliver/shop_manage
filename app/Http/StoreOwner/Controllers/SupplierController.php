<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreSupplier;
use App\Models\Department;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupplierController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Suppliers module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Suppliers')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of suppliers.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->orderBy('supplierid', 'DESC')
            ->get();
        
        return view('storeowner.suppliers.index', compact('storeSuppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function add(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get departments for the store (including storeid = 0 for global departments)
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        return view('storeowner.suppliers.add', compact('departments'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit($supplierid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $supplierid = base64_decode($supplierid);
        $storeSupplier = StoreSupplier::findOrFail($supplierid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify supplier belongs to store
        if ($storeSupplier->storeid != $storeid) {
            return redirect()->route('storeowner.suppliers.index')
                ->with('error', 'Unauthorized access.');
        }
        
        // Get departments for the store (including storeid = 0 for global departments)
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        return view('storeowner.suppliers.edit', compact('storeSupplier', 'departments'));
    }

    /**
     * Store a newly created supplier or update an existing one.
     */
    public function update(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->has('supplierid') && !empty($request->supplierid)) {
            // Update existing
            $validated = $request->validate([
                'supplierid' => 'required|string',
                'departmentid' => 'required|integer|exists:stoma_store_department,departmentid',
                'purchase_supplier' => 'required|in:Yes,No',
                'supplier_name' => 'required|string|max:255',
                'supplier_phone' => 'required|string|max:255',
                'supplier_phone2' => 'nullable|string|max:255',
                'supplier_email' => 'required|email|max:255',
                'supplier_rep' => 'required|string|max:255',
            ]);
            
            $supplierid = base64_decode($validated['supplierid']);
            $supplier = StoreSupplier::findOrFail($supplierid);
            
            // Verify supplier belongs to store
            if ($supplier->storeid != $storeid) {
                return redirect()->route('storeowner.suppliers.index')
                    ->with('error', 'Unauthorized access.');
            }
            
            $supplier->departmentid = $validated['departmentid'];
            $supplier->purchase_supplier = $validated['purchase_supplier'];
            $supplier->supplier_name = $validated['supplier_name'];
            $supplier->supplier_phone = $validated['supplier_phone'];
            $supplier->supplier_phone2 = $validated['supplier_phone2'] ?? null;
            $supplier->supplier_email = $validated['supplier_email'];
            $supplier->supplier_rep = $validated['supplier_rep'];
            $supplier->editdate_supplier = now();
            $supplier->editip = $request->ip();
            $supplier->save();
            
            return redirect()->route('storeowner.suppliers.index')
                ->with('success', 'Supplier Updated Successfully.');
        } else {
            // Create new
            $validated = $request->validate([
                'departmentid' => 'required|integer|exists:stoma_store_department,departmentid',
                'purchase_supplier' => 'required|in:Yes,No',
                'supplier_name' => 'required|string|max:255',
                'supplier_phone' => 'required|string|max:255',
                'supplier_phone2' => 'nullable|string|max:255',
                'supplier_email' => 'required|email|max:255',
                'supplier_rep' => 'required|string|max:255',
            ]);
            
            StoreSupplier::create([
                'storeid' => $storeid,
                'departmentid' => $validated['departmentid'],
                'purchase_supplier' => $validated['purchase_supplier'],
                'supplier_name' => $validated['supplier_name'],
                'supplier_phone' => $validated['supplier_phone'],
                'supplier_phone2' => $validated['supplier_phone2'] ?? null,
                'supplier_email' => $validated['supplier_email'],
                'supplier_rep' => $validated['supplier_rep'],
                'supplier_acc_number' => '0',
                'status' => 'Enable',
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            return redirect()->route('storeowner.suppliers.index')
                ->with('success', 'Supplier Added Successfully.');
        }
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy($supplierid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $supplierid = base64_decode($supplierid);
        $supplier = StoreSupplier::findOrFail($supplierid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify supplier belongs to store
        if ($supplier->storeid != $storeid) {
            return redirect()->route('storeowner.suppliers.index')
                ->with('error', 'Unauthorized access.');
        }
        
        $supplier->delete();
        
        return redirect()->route('storeowner.suppliers.index')
            ->with('success', 'Supplier has been deleted successfully');
    }

    /**
     * Change supplier status.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'supplierid' => 'required|string',
            'status' => 'required|in:Enable,Disable',
        ]);
        
        $supplierid = base64_decode($validated['supplierid']);
        $supplier = StoreSupplier::findOrFail($supplierid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify supplier belongs to store
        if ($supplier->storeid != $storeid) {
            return redirect()->route('storeowner.suppliers.index')
                ->with('error', 'Unauthorized access.');
        }
        
        $supplier->status = $validated['status'];
        $supplier->save();
        
        return redirect()->route('storeowner.suppliers.index')
            ->with('success', 'Status Changed Successfully !');
    }
}

