<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CatalogProductGroup;
use App\Models\ProductShipment;
use App\Models\PurchasePaymentMethod;
use App\Models\PurchaseMeasure;
use App\Models\TaxSetting;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupplierSettingController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Suppliers module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Suppliers')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display suppliers settings page.
     */
    public function settings(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $catalogProductGroups = CatalogProductGroup::where('storeid', $storeid)->get();
        $productshipments = ProductShipment::where('storeid', $storeid)->get();
        $purchasePaymentMethods = PurchasePaymentMethod::where('storeid', $storeid)->get();
        $purchaseMeasures = PurchaseMeasure::where('storeid', $storeid)->get();
        $taxSettings = TaxSetting::where('storeid', $storeid)->get();
        
        return view('storeowner.suppliers.settings', compact(
            'catalogProductGroups',
            'productshipments',
            'purchasePaymentMethods',
            'purchaseMeasures',
            'taxSettings'
        ));
    }

    // ============ Catalog Product Groups ============
    
    /**
     * Store or update catalog product group.
     */
    public function updateCatalogGroup(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('catalog_product_groupid') && !empty($request->catalog_product_groupid)) {
            // Update existing
            $validated = $request->validate([
                'catalog_product_groupid' => 'required|string',
                'catalog_product_group_name' => 'required|string|max:255',
            ]);
            
            $catalog_product_groupid = base64_decode($validated['catalog_product_groupid']);
            $group = CatalogProductGroup::findOrFail($catalog_product_groupid);
            
            $group->catalog_product_group_name = $validated['catalog_product_group_name'];
            $group->editdate = now();
            $group->editip = $request->ip();
            $group->save();
            
            $message = 'Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'catalog_product_group_name' => 'required|string|max:255',
            ]);
            
            CatalogProductGroup::create([
                'storeid' => $storeid,
                'catalog_product_group_name' => $validated['catalog_product_group_name'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Added Successfully.';
        }
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a catalog product group.
     */
    public function editCatalogGroup($catalog_product_groupid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $catalog_product_groupid = base64_decode($catalog_product_groupid);
        $group = CatalogProductGroup::findOrFail($catalog_product_groupid);
        
        return view('storeowner.suppliers.edit_catalog_group', compact('group'));
    }

    /**
     * Remove the specified catalog product group.
     */
    public function deleteCatalogGroup($catalog_product_groupid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $catalog_product_groupid = base64_decode($catalog_product_groupid);
        $group = CatalogProductGroup::findOrFail($catalog_product_groupid);
        $group->delete();
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', 'Group has been deleted successfully');
    }

    // ============ Product Shipments ============
    
    /**
     * Store or update product shipment.
     */
    public function updateShipment(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('shipmentid') && !empty($request->shipmentid)) {
            // Update existing
            $validated = $request->validate([
                'shipmentid' => 'required|string',
                'shipment' => 'required|string|max:255',
            ]);
            
            $shipmentid = base64_decode($validated['shipmentid']);
            $shipment = ProductShipment::findOrFail($shipmentid);
            
            $shipment->shipment = $validated['shipment'];
            $shipment->editdate = now();
            $shipment->editip = $request->ip();
            $shipment->save();
            
            $message = 'Shipment Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'shipment' => 'required|string|max:255',
            ]);
            
            ProductShipment::create([
                'storeid' => $storeid,
                'shipment' => $validated['shipment'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Shipment Added Successfully.';
        }
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a product shipment.
     */
    public function editShipment($shipmentid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $shipmentid = base64_decode($shipmentid);
        $shipment = ProductShipment::findOrFail($shipmentid);
        
        return view('storeowner.suppliers.edit_shipment', compact('shipment'));
    }

    /**
     * Remove the specified product shipment.
     */
    public function deleteShipment($shipmentid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $shipmentid = base64_decode($shipmentid);
        $shipment = ProductShipment::findOrFail($shipmentid);
        $shipment->delete();
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', 'Shipment type has been deleted successfully');
    }

    // ============ Purchase Payment Methods ============
    
    /**
     * Store or update purchase payment method.
     */
    public function updatePaymentMethod(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('purchasepaymentmethodid') && !empty($request->purchasepaymentmethodid)) {
            // Update existing
            $validated = $request->validate([
                'purchasepaymentmethodid' => 'required|string',
                'paymentmethod' => 'required|string|max:255',
            ]);
            
            $purchasepaymentmethodid = base64_decode($validated['purchasepaymentmethodid']);
            $paymentMethod = PurchasePaymentMethod::findOrFail($purchasepaymentmethodid);
            
            $paymentMethod->paymentmethod = $validated['paymentmethod'];
            $paymentMethod->editdate = now();
            $paymentMethod->editip = $request->ip();
            $paymentMethod->save();
            
            $message = 'Payment Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'paymentmethod' => 'required|string|max:255',
            ]);
            
            PurchasePaymentMethod::create([
                'storeid' => $storeid,
                'paymentmethod' => $validated['paymentmethod'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Payment Added Successfully.';
        }
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a purchase payment method.
     */
    public function editPaymentMethod($purchasepaymentmethodid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $purchasepaymentmethodid = base64_decode($purchasepaymentmethodid);
        $paymentMethod = PurchasePaymentMethod::findOrFail($purchasepaymentmethodid);
        
        return view('storeowner.suppliers.edit_payment_method', compact('paymentMethod'));
    }

    /**
     * Remove the specified purchase payment method.
     */
    public function deletePaymentMethod($purchasepaymentmethodid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $purchasepaymentmethodid = base64_decode($purchasepaymentmethodid);
        $paymentMethod = PurchasePaymentMethod::findOrFail($purchasepaymentmethodid);
        $paymentMethod->delete();
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', 'Purchase Payment type has been deleted successfully');
    }

    // ============ Purchase Measures ============
    
    /**
     * Store or update purchase measure.
     */
    public function updateMeasure(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('purchasemeasuresid') && !empty($request->purchasemeasuresid)) {
            // Update existing
            $validated = $request->validate([
                'purchasemeasuresid' => 'required|string',
                'purchasemeasure' => 'required|string|max:255',
            ]);
            
            $purchasemeasuresid = base64_decode($validated['purchasemeasuresid']);
            $measure = PurchaseMeasure::findOrFail($purchasemeasuresid);
            
            $measure->purchasemeasure = $validated['purchasemeasure'];
            $measure->editdate_pm = now();
            $measure->editip = $request->ip();
            $measure->save();
            
            $message = 'Measure Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'purchasemeasure' => 'required|string|max:255',
            ]);
            
            PurchaseMeasure::create([
                'storeid' => $storeid,
                'purchasemeasure' => $validated['purchasemeasure'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Measure Added Successfully.';
        }
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a purchase measure.
     */
    public function editMeasure($purchasemeasuresid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $purchasemeasuresid = base64_decode($purchasemeasuresid);
        $measure = PurchaseMeasure::findOrFail($purchasemeasuresid);
        
        return view('storeowner.suppliers.edit_measure', compact('measure'));
    }

    /**
     * Remove the specified purchase measure.
     */
    public function deleteMeasure($purchasemeasuresid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $purchasemeasuresid = base64_decode($purchasemeasuresid);
        $measure = PurchaseMeasure::findOrFail($purchasemeasuresid);
        $measure->delete();
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', 'Purchase measure has been deleted successfully');
    }

    // ============ Tax Settings ============
    
    /**
     * Store or update tax setting.
     */
    public function updateTax(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('taxid') && !empty($request->taxid)) {
            // Update existing
            $validated = $request->validate([
                'taxid' => 'required|string',
                'tax_name' => 'required|string|max:255',
                'tax_amount' => 'required|string|max:255',
            ]);
            
            $taxid = base64_decode($validated['taxid']);
            $tax = TaxSetting::findOrFail($taxid);
            
            $tax->tax_name = $validated['tax_name'];
            $tax->tax_amount = $validated['tax_amount'];
            $tax->editdate_tax = now();
            $tax->editip = $request->ip();
            $tax->save();
            
            $message = 'Tax Settings Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'tax_name' => 'required|string|max:255',
                'tax_amount' => 'required|string|max:255',
            ]);
            
            TaxSetting::create([
                'storeid' => $storeid,
                'tax_name' => $validated['tax_name'],
                'tax_amount' => $validated['tax_amount'],
                'insertby' => $user->userid ?? 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Tax Added Successfully.';
        }
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a tax setting.
     */
    public function editTax($taxid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $taxid = base64_decode($taxid);
        $tax = TaxSetting::findOrFail($taxid);
        
        return view('storeowner.suppliers.edit_tax', compact('tax'));
    }

    /**
     * Remove the specified tax setting.
     */
    public function deleteTax($taxid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $taxid = base64_decode($taxid);
        $tax = TaxSetting::findOrFail($taxid);
        $tax->delete();
        
        return redirect()->route('storeowner.suppliers.settings')
            ->with('success', 'Tax has been deleted successfully');
    }
}

