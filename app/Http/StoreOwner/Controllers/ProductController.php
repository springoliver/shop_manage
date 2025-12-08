<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreProduct;
use App\Models\StoreSupplier;
use App\Models\Department;
use App\Models\CatalogProductGroup;
use App\Models\ProductShipment;
use App\Models\PurchasePaymentMethod;
use App\Models\PurchaseMeasure;
use App\Models\TaxSetting;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Products module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Products')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of products.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $storeProducts = DB::table('stoma_store_products as sp')
            ->select(
                'sp.*',
                'ss.supplier_name',
                'ts.tax_name',
                'pm.purchasemeasure'
            )
            ->leftJoin('stoma_store_suppliers as ss', 'sp.supplierid', '=', DB::raw('ss.supplierid'))
            ->leftJoin('stoma_tax_settings as ts', 'sp.taxid', '=', 'ts.taxid')
            ->leftJoin('stoma_purchasemeasures as pm', 'sp.purchasemeasuresid', '=', 'pm.purchasemeasuresid')
            ->where('sp.storeid', $storeid)
            ->where(function($query) {
                $query->where('ss.status', 'Enable')
                      ->orWhereNull('ss.status');
            })
            ->where('sp.product_status', 'Enable')
            ->orderBy('sp.productid', 'DESC')
            ->paginate(10);
        
        return view('storeowner.products.index', compact('storeProducts'));
    }

    /**
     * Display products by supplier.
     */
    public function bySupplier($supplierid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $supplierid = base64_decode($supplierid);
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get supplier info
        $supplier = StoreSupplier::where('supplierid', $supplierid)
            ->where('storeid', $storeid)
            ->firstOrFail();
        
        // Get products for this supplier
        $storeProducts = DB::table('stoma_store_products as sp')
            ->select(
                'sp.*',
                'ss.supplier_name',
                'ts.tax_name',
                'pm.purchasemeasure'
            )
            ->leftJoin('stoma_store_suppliers as ss', 'sp.supplierid', '=', DB::raw('ss.supplierid'))
            ->leftJoin('stoma_tax_settings as ts', 'sp.taxid', '=', 'ts.taxid')
            ->leftJoin('stoma_purchasemeasures as pm', 'sp.purchasemeasuresid', '=', 'pm.purchasemeasuresid')
            ->where('sp.storeid', $storeid)
            ->where('sp.supplierid', (string)$supplierid)
            ->orderBy('sp.productid', 'DESC')
            ->paginate(10);
        
        return view('storeowner.products.by_supplier', compact('storeProducts', 'supplier'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function add(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all dropdown data
        $catalogProductGroups = CatalogProductGroup::where('storeid', $storeid)->get();
        
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->get();
        
        $productshipments = ProductShipment::where('storeid', $storeid)->get();
        $purchasePaymentMethods = PurchasePaymentMethod::where('storeid', $storeid)->get();
        $purchaseMeasures = PurchaseMeasure::where('storeid', $storeid)->get();
        $taxSettings = TaxSetting::where('storeid', $storeid)->get();
        
        return view('storeowner.products.add', compact(
            'catalogProductGroups',
            'departments',
            'storeSuppliers',
            'productshipments',
            'purchasePaymentMethods',
            'purchaseMeasures',
            'taxSettings'
        ));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($productid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $productid = base64_decode($productid);
        $storeProduct = StoreProduct::findOrFail($productid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify product belongs to store
        if ($storeProduct->storeid != $storeid) {
            return redirect()->route('storeowner.products.index')
                ->with('error', 'Unauthorized access.');
        }
        
        // Get all dropdown data
        $catalogProductGroups = CatalogProductGroup::where('storeid', $storeid)->get();
        
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->get();
        
        $productshipments = ProductShipment::where('storeid', $storeid)->get();
        $purchasePaymentMethods = PurchasePaymentMethod::where('storeid', $storeid)->get();
        $purchaseMeasures = PurchaseMeasure::where('storeid', $storeid)->get();
        $taxSettings = TaxSetting::where('storeid', $storeid)->get();
        
        return view('storeowner.products.edit', compact(
            'storeProduct',
            'catalogProductGroups',
            'departments',
            'storeSuppliers',
            'productshipments',
            'purchasePaymentMethods',
            'purchaseMeasures',
            'taxSettings'
        ));
    }

    /**
     * Store a newly created product or update an existing one.
     */
    public function update(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->has('productid') && !empty($request->productid)) {
            // Update existing
            $validated = $request->validate([
                'productid' => 'required|string',
                'catalog_product_groupid' => 'required|integer|exists:stoma_catalog_product_group,catalog_product_groupid',
                'departmentid' => 'required|integer|exists:stoma_store_department,departmentid',
                'supplierid' => 'required|string',
                'product_name' => 'required|string|max:255',
                'taxid' => 'required|integer|exists:stoma_tax_settings,taxid',
                'product_price' => 'required|string|max:255',
                'product_notes' => 'nullable|string|max:255',
                'shipmentid' => 'required|integer|exists:stoma_productshipment,shipmentid',
                'purchasepaymentmethodid' => 'required|integer|exists:stoma_purchasepaymentmethod,purchasepaymentmethodid',
                'purchasemeasuresid' => 'required|integer|exists:stoma_purchasemeasures,purchasemeasuresid',
            ]);
            
            $productid = base64_decode($validated['productid']);
            $product = StoreProduct::findOrFail($productid);
            
            // Verify product belongs to store
            if ($product->storeid != $storeid) {
                return redirect()->route('storeowner.products.index')
                    ->with('error', 'Unauthorized access.');
            }
            
            $product->catalog_product_groupid = $validated['catalog_product_groupid'];
            $product->departmentid = $validated['departmentid'];
            $product->supplierid = $validated['supplierid'];
            $product->product_name = $validated['product_name'];
            $product->taxid = $validated['taxid'];
            $product->product_price = $validated['product_price'];
            $product->product_notes = $validated['product_notes'] ?? '';
            $product->shipmentid = $validated['shipmentid'];
            $product->purchasepaymentmethodid = $validated['purchasepaymentmethodid'];
            $product->purchasemeasuresid = $validated['purchasemeasuresid'];
            $product->editdate = now()->toDateString();
            $product->editip = $request->ip();
            $product->save();
            
            return redirect()->route('storeowner.products.index')
                ->with('success', 'Product has been updated successfully');
        } else {
            // Create new
            $validated = $request->validate([
                'catalog_product_groupid' => 'required|integer|exists:stoma_catalog_product_group,catalog_product_groupid',
                'departmentid' => 'required|integer|exists:stoma_store_department,departmentid',
                'supplierid' => 'required|string',
                'product_name' => 'required|string|max:255',
                'taxid' => 'required|integer|exists:stoma_tax_settings,taxid',
                'product_price' => 'required|string|max:255',
                'product_notes' => 'nullable|string|max:255',
                'shipmentid' => 'required|integer|exists:stoma_productshipment,shipmentid',
                'purchasepaymentmethodid' => 'required|integer|exists:stoma_purchasepaymentmethod,purchasepaymentmethodid',
                'purchasemeasuresid' => 'required|integer|exists:stoma_purchasemeasures,purchasemeasuresid',
            ]);
            
            StoreProduct::create([
                'storeid' => $storeid,
                'catalog_product_groupid' => $validated['catalog_product_groupid'],
                'departmentid' => $validated['departmentid'],
                'supplierid' => $validated['supplierid'],
                'product_name' => $validated['product_name'],
                'product_status' => 'Enable',
                'taxid' => $validated['taxid'],
                'product_price' => $validated['product_price'],
                'product_notes' => $validated['product_notes'] ?? '',
                'shipmentid' => $validated['shipmentid'],
                'purchasepaymentmethodid' => $validated['purchasepaymentmethodid'],
                'purchasemeasuresid' => $validated['purchasemeasuresid'],
                'insertby' => $user->userid ?? null,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            return redirect()->route('storeowner.products.index')
                ->with('success', 'Product Added successfully');
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy($productid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $productid = base64_decode($productid);
        $product = StoreProduct::findOrFail($productid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify product belongs to store
        if ($product->storeid != $storeid) {
            return redirect()->route('storeowner.products.index')
                ->with('error', 'Unauthorized access.');
        }
        
        $product->delete();
        
        return redirect()->route('storeowner.products.index')
            ->with('success', 'Product has been deleted successfully');
    }

    /**
     * Change product status.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'productid' => 'required|string',
            'product_status' => 'required|in:Enable,Disable',
        ]);
        
        $productid = base64_decode($validated['productid']);
        $product = StoreProduct::findOrFail($productid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify product belongs to store
        if ($product->storeid != $storeid) {
            return redirect()->back()
                ->with('error', 'Unauthorized access.');
        }
        
        $product->product_status = $validated['product_status'];
        $product->save();
        
        $referrer = $request->header('referer');
        if ($referrer) {
            return redirect($referrer)
                ->with('success', 'Status updated successfully');
        }
        
        return redirect()->route('storeowner.products.index')
            ->with('success', 'Status updated successfully');
    }

    /**
     * Change product price.
     */
    public function changePrice(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'productid' => 'required|string',
            'product_price' => 'required|string|max:255',
        ]);
        
        $productid = base64_decode($validated['productid']);
        $product = StoreProduct::findOrFail($productid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Verify product belongs to store
        if ($product->storeid != $storeid) {
            return redirect()->back()
                ->with('error', 'Unauthorized access.');
        }
        
        $product->product_price = $validated['product_price'];
        $product->editdate = now()->toDateString();
        $product->editip = $request->ip();
        $product->save();
        
        $referrer = $request->header('referer');
        if ($referrer) {
            return redirect($referrer)
                ->with('success', 'Price updated successfully');
        }
        
        return redirect()->route('storeowner.products.index')
            ->with('success', 'Price updated successfully');
    }
}

