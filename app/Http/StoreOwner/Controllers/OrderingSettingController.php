<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderCategory;
use App\Models\SupplierDocType;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderingSettingController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Ordering module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Ordering')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display the settings page.
     */
    public function settings(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $purchaseCategories = PurchaseOrderCategory::where('storeid', $storeid)
            ->orderBy('categoryid', 'DESC')
            ->get();
        
        $supplierDocTypes = SupplierDocType::where('storeid', $storeid)
            ->orderBy('docs_type_id', 'DESC')
            ->get();
        
        return view('storeowner.ordering.settings', compact('purchaseCategories', 'supplierDocTypes'));
    }

    /**
     * Create or update a purchase order category.
     */
    public function updateCategory(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->has('categoryid') && !empty($request->categoryid)) {
            // Update existing
            $validated = $request->validate([
                'categoryid' => 'required|string',
                'category_name' => 'required|string|max:255',
            ]);
            
            $categoryid = base64_decode($validated['categoryid']);
            $category = PurchaseOrderCategory::findOrFail($categoryid);
            
            // Verify category belongs to store
            if ($category->storeid != $storeid) {
                return redirect()->route('storeowner.ordering.settings')
                    ->with('error', 'Unauthorized access.');
            }
            
            $category->category_name = $validated['category_name'];
            $category->editdate = now();
            $category->editip = $request->ip();
            $category->editby = $user->userid ?? 0;
            $category->save();
            
            return redirect()->route('storeowner.ordering.settings')
                ->with('success', 'Category Updated Successfully.');
        } else {
            // Create new
            $validated = $request->validate([
                'category_name' => 'required|string|max:255',
            ]);
            
            PurchaseOrderCategory::create([
                'storeid' => $storeid,
                'category_name' => $validated['category_name'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'editdate' => now(),
                'editip' => $request->ip(),
                'editby' => 0,
            ]);
            
            return redirect()->route('storeowner.ordering.settings')
                ->with('success', 'Category Added Successfully.');
        }
    }

    /**
     * Show the form for editing a category.
     */
    public function editCategory($categoryid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $categoryid = base64_decode($categoryid);
        $category = PurchaseOrderCategory::findOrFail($categoryid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify category belongs to store
        if ($category->storeid != $storeid) {
            return redirect()->route('storeowner.ordering.settings')
                ->with('error', 'Unauthorized access.');
        }
        
        return view('storeowner.ordering.edit_category', compact('category'));
    }

    /**
     * Remove the specified category.
     */
    public function deleteCategory($categoryid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $categoryid = base64_decode($categoryid);
        $category = PurchaseOrderCategory::findOrFail($categoryid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify category belongs to store
        if ($category->storeid != $storeid) {
            return redirect()->route('storeowner.ordering.settings')
                ->with('error', 'Unauthorized access.');
        }
        
        $category->delete();
        
        return redirect()->route('storeowner.ordering.settings')
            ->with('success', 'Category has been deleted successfully');
    }

    /**
     * Create or update a supplier document type.
     */
    public function updateDocType(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->has('docs_type_id') && !empty($request->docs_type_id)) {
            // Update existing
            $validated = $request->validate([
                'docs_type_id' => 'required|string',
                'docs_type_name' => 'required|string|max:255',
            ]);
            
            $docs_type_id = base64_decode($validated['docs_type_id']);
            $docType = SupplierDocType::findOrFail($docs_type_id);
            
            // Verify doc type belongs to store
            if ($docType->storeid != $storeid) {
                return redirect()->route('storeowner.ordering.settings')
                    ->with('error', 'Unauthorized access.');
            }
            
            $docType->docs_type_name = $validated['docs_type_name'];
            $docType->editdate = now();
            $docType->editip = $request->ip();
            $docType->editby = $user->userid ?? 0;
            $docType->save();
            
            return redirect()->route('storeowner.ordering.settings')
                ->with('success', 'Document Type Updated Successfully.');
        } else {
            // Create new
            $validated = $request->validate([
                'docs_type_name' => 'required|string|max:255',
            ]);
            
            SupplierDocType::create([
                'storeid' => $storeid,
                'docs_type_name' => $validated['docs_type_name'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'editdate' => now(),
                'editip' => $request->ip(),
                'editby' => 0,
            ]);
            
            return redirect()->route('storeowner.ordering.settings')
                ->with('success', 'Supplier Type Added Successfully.');
        }
    }

    /**
     * Show the form for editing a supplier document type.
     */
    public function editDocType($docs_type_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $docs_type_id = base64_decode($docs_type_id);
        $docType = SupplierDocType::findOrFail($docs_type_id);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify doc type belongs to store
        if ($docType->storeid != $storeid) {
            return redirect()->route('storeowner.ordering.settings')
                ->with('error', 'Unauthorized access.');
        }
        
        return view('storeowner.ordering.edit_doc_type', compact('docType'));
    }

    /**
     * Remove the specified supplier document type.
     */
    public function deleteDocType($docs_type_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $docs_type_id = base64_decode($docs_type_id);
        $docType = SupplierDocType::findOrFail($docs_type_id);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify doc type belongs to store
        if ($docType->storeid != $storeid) {
            return redirect()->route('storeowner.ordering.settings')
                ->with('error', 'Unauthorized access.');
        }
        
        $docType->delete();
        
        return redirect()->route('storeowner.ordering.settings')
            ->with('success', 'Category has been deleted successfully');
    }
}

