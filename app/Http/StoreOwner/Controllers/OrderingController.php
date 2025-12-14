<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchasedProduct;
use App\Models\StoreSupplier;
use App\Models\Department;
use App\Models\StoreProduct;
use App\Models\PurchaseOrderCategory;
use App\Models\SupplierDocument;
use App\Models\SupplierDocType;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderingController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Ordering module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Ordering')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display the new purchase order page.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get suppliers where purchase_supplier = 'Yes' and status = 'Enable'
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->where('purchase_supplier', 'Yes')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        // Get departments
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        return view('storeowner.ordering.index', compact('storeSuppliers', 'departments'));
    }

    /**
     * Display the new purchase order page or store a newly created purchase order.
     */
    public function order(Request $request): RedirectResponse|View
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $username = $user->username ?? '';
        
        // If POST request with submit, create purchase order
        if ($request->isMethod('post') && $request->has('submit')) {
            $validated = $request->validate([
                'order_supplier_id' => 'required|integer|exists:stoma_store_suppliers,supplierid',
                'delivery_date' => 'required|date',
                'order_total_price' => 'required|numeric',
                'order_total_tax' => 'required|numeric',
                'order_total_inc_tax' => 'required|numeric',
                'productid' => 'required|array|min:1',
                'productid.*' => 'required|string',
                'quantity' => 'required|array',
                'quantity.*' => 'required|integer|min:1',
                'product_price' => 'required|array',
                'product_price.*' => 'required|numeric',
                'taxid' => 'required|array',
                'taxid.*' => 'required|integer',
                'product_total' => 'required|array',
                'product_total.*' => 'required|numeric',
                'departmentid' => 'required|array',
                'departmentid.*' => 'required|integer',
                'supplierid' => 'required|array',
                'supplierid.*' => 'required|string',
                'shipmentid' => 'required|array',
                'shipmentid.*' => 'required|integer',
                'purchasemeasuresid' => 'required|array',
                'purchasemeasuresid.*' => 'required|integer',
                'po_note' => 'nullable|string|max:255',
            ]);
            
            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'storeid' => $storeid,
                'departmentid' => $validated['departmentid'][0],
                'supplierid' => $validated['order_supplier_id'],
                'categoryid' => 0,
                'shipmentid' => $validated['shipmentid'][0],
                'deliverydocketstatus' => 'No',
                'invoicestatus' => 'No',
                'status' => 'No',
                'total_amount' => $validated['order_total_price'],
                'total_tax' => $validated['order_total_tax'],
                'amount_inc_tax' => $validated['order_total_inc_tax'],
                'delivery_date' => $validated['delivery_date'],
                'po_note' => $validated['po_note'] ?? null,
                'purchase_orders_type' => 'Purchase order',
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'insertby' => $username,
                'editdate' => now(),
                'editip' => '',
                'editby' => '0',
            ]);
            
            $purchaseOrderId = $purchaseOrder->purchase_orders_id;
            
            // Create Purchased Products
            $productIds = $validated['productid'];
            $quantities = $validated['quantity'];
            $productPrices = $validated['product_price'];
            $taxIds = $validated['taxid'];
            $productTotals = $validated['product_total'];
            $departmentIds = $validated['departmentid'];
            $supplierIds = $validated['supplierid'];
            $shipmentIds = $validated['shipmentid'];
            $purchasemeasuresIds = $validated['purchasemeasuresid'];
            
            foreach ($productIds as $i => $productid) {
                PurchasedProduct::create([
                    'purchase_orders_id' => $purchaseOrderId,
                    'storeid' => $storeid,
                    'departmentid' => $departmentIds[$i],
                    'supplierid' => (int)$supplierIds[$i],
                    'shipmentid' => $shipmentIds[$i],
                    'productid' => $productid,
                    'deliverydocketstatus' => 'No',
                    'invoicestatus' => 'No',
                    'quantity' => $quantities[$i],
                    'product_price' => $productPrices[$i],
                    'taxid' => $taxIds[$i],
                    'totalamount' => $productTotals[$i],
                    'purchasemeasuresid' => $purchasemeasuresIds[$i],
                    'purchase_orders_type' => '',
                    'insertdate' => now(),
                    'insertip' => $request->ip(),
                    'insertby' => $username,
                    'editdate' => now(),
                    'editip' => '',
                    'editby' => '0',
                ]);
            }
            
            return redirect()->route('storeowner.ordering.index')
                ->with('success', 'Purchase Order Created Successfully. <strong>Purchase Order ID: ' . $purchaseOrderId . '</strong>');
        }
        
        // If GET request, display the form (same as index)
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->where('purchase_supplier', 'Yes')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        $departments = Department::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })
        ->where('status', 'Enable')
        ->get();
        
        return view('storeowner.ordering.index', compact('storeSuppliers', 'departments'));
    }

    /**
     * Display orders waiting approval.
     */
    public function waitingApproval(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $username = $user->username ?? '';
        
        // Handle Order Complete (POST)
        if ($request->isMethod('post') && $request->has('submit')) {
            $validated = $request->validate([
                'purchase_orders_id' => 'required|integer|exists:stoma_purchase_orders,purchase_orders_id',
                'invoicenumber' => 'required|string|max:255',
            ]);
            
            $purchaseOrder = PurchaseOrder::find($validated['purchase_orders_id']);
            if ($purchaseOrder) {
                $purchaseOrder->update([
                    'invoicenumber' => $validated['invoicenumber'],
                    'status' => 'Yes',
                    'editdate' => now(),
                    'editip' => $request->ip(),
                    'editby' => $username,
                ]);
                
                return redirect()->route('storeowner.ordering.waiting_approval')
                    ->with('success', 'Purchase Order Completed Successfully');
            }
            
            return redirect()->route('storeowner.ordering.waiting_approval')
                ->with('error', 'Something went wrong please try again.');
        }
        
        // Get orders waiting approval (status = 'No')
        $ordersWaitingApproval = PurchaseOrder::with('supplier')
            ->where('storeid', $storeid)
            ->where('status', 'No')
            ->orderBy('purchase_orders_id', 'DESC')
            ->get();
        
        return view('storeowner.ordering.waiting_approval', compact('ordersWaitingApproval'));
    }

    /**
     * Display Purchase Orders Report.
     */
    public function report(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        if ($request->has('submit')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $supplierId = $request->input('supplierid');
            $purchaseOrdersType = $request->input('purchase_orders_type');
            
            $query = PurchaseOrder::with(['supplier', 'category'])
                ->where('storeid', $storeid)
                ->where('status', 'Yes');
            
            if ($dateFrom) {
                $query->whereDate('delivery_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('delivery_date', '<=', $dateTo);
            }
            if ($supplierId) {
                $query->where('supplierid', $supplierId);
            }
            if ($purchaseOrdersType) {
                $query->where('purchase_orders_type', $purchaseOrdersType);
            }
            
            $reports = $query->orderBy('delivery_date', 'DESC')->get();
        } else {
            // Default: last month's orders
            $reports = PurchaseOrder::with(['supplier', 'category'])
                ->where('storeid', $storeid)
                ->where('status', 'Yes')
                ->whereMonth('delivery_date', date('m'))
                ->orderBy('delivery_date', 'DESC')
                ->limit(10)
                ->get();
        }
        
        // Add total_products count for each order
        foreach ($reports as $report) {
            $report->total_products = PurchasedProduct::where('purchase_orders_id', $report->purchase_orders_id)->count();
        }
        
        return view('storeowner.ordering.report', compact('reports', 'storeSuppliers'));
    }

    /**
     * Display Purchased Products Report.
     */
    public function productReport(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        if ($request->has('submit')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $supplierId = $request->input('supplierid');
            
            $query = PurchasedProduct::with(['product', 'supplier', 'department'])
                ->where('storeid', $storeid);
            
            if ($dateFrom) {
                $query->whereDate('insertdate', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('insertdate', '<=', $dateTo);
            }
            if ($supplierId) {
                $query->where('supplierid', $supplierId);
            }
            
            $productReports = $query->orderBy('insertdate', 'DESC')->get();
        } else {
            // Default: last month's products
            $productReports = PurchasedProduct::with(['product', 'supplier', 'department'])
                ->where('storeid', $storeid)
                ->whereMonth('insertdate', date('m'))
                ->orderBy('insertdate', 'DESC')
                ->get();
        }
        
        return view('storeowner.ordering.product_report', compact('productReports', 'storeSuppliers'));
    }

    /**
     * Display Missing Delivery Dockets Report.
     */
    public function missingDeliveryDockets(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        if ($request->has('submit')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $supplierId = $request->input('supplierid');
            
            $query = PurchaseOrder::with('supplier')
                ->where('storeid', $storeid)
                ->where('deliverydocketstatus', 'No');
            
            if ($dateFrom) {
                $query->whereDate('insertdate', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('insertdate', '<=', $dateTo);
            }
            if ($supplierId) {
                $query->where('supplierid', $supplierId);
            }
            
            $reports = $query->orderBy('purchase_orders_id', 'DESC')->get();
        } else {
            $reports = PurchaseOrder::with('supplier')
                ->where('storeid', $storeid)
                ->where('deliverydocketstatus', 'No')
                ->orderBy('purchase_orders_id', 'DESC')
                ->get();
        }
        
        // Add total_products count
        foreach ($reports as $report) {
            $report->total_products = PurchasedProduct::where('purchase_orders_id', $report->purchase_orders_id)->count();
        }
        
        return view('storeowner.ordering.missing_delivery_dockets', compact('reports', 'storeSuppliers'));
    }

    /**
     * Display Credit Notes Report.
     */
    public function creditNotes(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        if ($request->has('submit')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $supplierId = $request->input('supplierid');
            
            $query = PurchaseOrder::with('supplier')
                ->where('storeid', $storeid)
                ->where('creditnote', 'Yes');
            
            if ($dateFrom) {
                $query->whereDate('insertdate', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('insertdate', '<=', $dateTo);
            }
            if ($supplierId) {
                $query->where('supplierid', $supplierId);
            }
            
            $reports = $query->orderBy('purchase_orders_id', 'DESC')->get();
        } else {
            $reports = PurchaseOrder::with('supplier')
                ->where('storeid', $storeid)
                ->where('creditnote', 'Yes')
                ->orderBy('purchase_orders_id', 'DESC')
                ->get();
        }
        
        // Add total_products count
        foreach ($reports as $report) {
            $report->total_products = PurchasedProduct::where('purchase_orders_id', $report->purchase_orders_id)->count();
        }
        
        return view('storeowner.ordering.credit_notes', compact('reports', 'storeSuppliers'));
    }

    /**
     * Update Delivery Docket Status.
     */
    public function updateDeliveryDockStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'purchase_orders_id' => 'required|integer|exists:stoma_purchase_orders,purchase_orders_id',
            'deliverydocketstatus' => 'required|in:Yes,No',
        ]);
        
        $storeid = $this->getStoreId();
        
        $purchaseOrder = PurchaseOrder::where('purchase_orders_id', $validated['purchase_orders_id'])
            ->where('storeid', $storeid)
            ->first();
        
        if ($purchaseOrder) {
            $purchaseOrder->update([
                'deliverydocketstatus' => $validated['deliverydocketstatus'],
            ]);
            
            return redirect()->back()
                ->with('success', 'Report status change Successfully.');
        }
        
            return redirect()->back()
                ->with('error', 'Error changing report status!!.');
    }

    /**
     * Display Tax Analysis page.
     */
    public function taxAnalysis(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get monthly totals grouped by month/year
        $allPurchOrdersTotal = PurchaseOrder::where('storeid', $storeid)
            ->where('status', 'Yes')
            ->select(
                DB::raw('MONTH(delivery_date) as pmonth'),
                DB::raw('YEAR(delivery_date) as pyear'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(total_tax) as total_tax'),
                DB::raw('SUM(amount_inc_tax) as amount_inc_tax'),
                DB::raw('MIN(delivery_date) as delivery_date')
            )
            ->groupBy('pmonth', 'pyear')
            ->orderBy('pyear', 'DESC')
            ->orderBy('pmonth', 'DESC')
            ->get();
        
        return view('storeowner.ordering.tax_analysis', compact('allPurchOrdersTotal'));
    }

    /**
     * Display Add Invoice/Bill page.
     */
    public function addInvoice(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get categories
        $purchaseOrdersCategory = PurchaseOrderCategory::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })->get();
        
        // Get bills from current month
        $reports = PurchaseOrder::with(['supplier', 'category'])
            ->where('storeid', $storeid)
            ->where('status', 'Yes')
            ->whereMonth('delivery_date', date('m'))
            ->whereYear('delivery_date', date('Y'))
            ->orderBy('delivery_date', 'DESC')
            ->get();
        
        return view('storeowner.ordering.add_invoice', compact('purchaseOrdersCategory', 'reports'));
    }

    /**
     * Display Add Bills for specific month.
     */
    public function addBills($delivery_date): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        try {
            $delivery_date = base64_decode($delivery_date);
        } catch (\Exception $e) {
            return redirect()->route('storeowner.ordering.tax_analysis')
                ->with('error', 'Invalid delivery date');
        }
        
        $storeid = $this->getStoreId();
        
        // Get categories
        $purchaseOrdersCategory = PurchaseOrderCategory::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })->get();
        
        // Get bills for the specific month
        $purchaseOrders = PurchaseOrder::with(['supplier', 'category'])
            ->where('storeid', $storeid)
            ->whereMonth('delivery_date', date('m', strtotime($delivery_date)))
            ->whereYear('delivery_date', date('Y', strtotime($delivery_date)))
            ->orderBy('delivery_date', 'DESC')
            ->get();
        
        return view('storeowner.ordering.add_bills', compact('purchaseOrders', 'purchaseOrdersCategory', 'delivery_date'));
    }

    /**
     * Display Edit Bills for a specific purchase order.
     */
    public function editBills($purchase_orders_id, $delivery_date): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        try {
            $purchase_orders_id = base64_decode($purchase_orders_id);
            $delivery_date = base64_decode($delivery_date);
        } catch (\Exception $e) {
            return redirect()->route('storeowner.ordering.tax_analysis')
                ->with('error', 'Invalid parameters');
        }
        
        $storeid = $this->getStoreId();
        
        // Get the purchase order to edit
        $purchaseOrderEdit = PurchaseOrder::with(['supplier', 'category'])
            ->where('purchase_orders_id', $purchase_orders_id)
            ->where('storeid', $storeid)
            ->first();
        
        if (!$purchaseOrderEdit) {
            return redirect()->route('storeowner.ordering.tax_analysis')
                ->with('error', 'Purchase order not found');
        }
        
        // Get categories
        $purchaseOrdersCategory = PurchaseOrderCategory::where(function($query) use ($storeid) {
            $query->where('storeid', $storeid)
                  ->orWhere('storeid', 0);
        })->get();
        
        // Get bills for the specific month
        $purchaseOrders = PurchaseOrder::with(['supplier', 'category'])
            ->where('storeid', $storeid)
            ->whereMonth('delivery_date', date('m', strtotime($delivery_date)))
            ->whereYear('delivery_date', date('Y', strtotime($delivery_date)))
            ->orderBy('delivery_date', 'DESC')
            ->get();
        
        return view('storeowner.ordering.add_bills', compact('purchaseOrders', 'purchaseOrdersCategory', 'delivery_date', 'purchaseOrderEdit'));
    }

    /**
     * Create or update a bill.
     */
    public function newBill(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $username = $user->username ?? '';
        
        // Check if updating existing bill
        if ($request->has('purchase_orders_id') && $request->input('purchase_orders_id')) {
            try {
                $purchaseOrdersId = base64_decode($request->input('purchase_orders_id'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Invalid purchase order ID');
            }
            
            $validated = $request->validate([
                'categoryid' => 'required|integer',
                'invoicenumber' => 'required|string|max:255',
                'invoicestatus' => 'required|in:Yes,No',
                'total_amount' => 'required|numeric',
                'total_tax' => 'required|numeric',
                'amount_inc_tax' => 'required|numeric',
                'products_bought' => 'nullable|string|max:255',
            ]);
            
            $purchaseOrder = PurchaseOrder::where('purchase_orders_id', $purchaseOrdersId)
                ->where('storeid', $storeid)
                ->first();
            
            if ($purchaseOrder) {
                $purchaseOrder->update([
                    'categoryid' => $validated['categoryid'],
                    'invoicenumber' => $validated['invoicenumber'],
                    'invoicestatus' => $validated['invoicestatus'],
                    'total_amount' => $validated['total_amount'],
                    'total_tax' => $validated['total_tax'],
                    'amount_inc_tax' => $validated['amount_inc_tax'],
                    'products_bought' => $validated['products_bought'] ?? null,
                    'editdate' => now(),
                    'editip' => $request->ip(),
                    'editby' => $username,
                ]);
                
                return redirect()->back()->with('success', 'Invoice Updated Successfully.');
            }
            
            return redirect()->back()->with('error', 'Something went wrong please try again.');
        } else {
            // Create new bill
            $validated = $request->validate([
                'delivery_date' => 'required|date',
                'categoryid' => 'required|integer',
                'invoicenumber' => 'required|string|max:255',
                'invoicestatus' => 'required|in:Yes,No',
                'total_amount' => 'required|numeric',
                'total_tax' => 'required|numeric',
                'amount_inc_tax' => 'required|numeric',
                'products_bought' => 'nullable|string|max:255',
                'departmentid' => 'nullable|integer',
            ]);
            
            // Get default department if not provided
            $departmentid = $validated['departmentid'] ?? null;
            if (!$departmentid) {
                $defaultDepartment = Department::where('storeid', $storeid)
                    ->orWhere('storeid', 0)
                    ->where('status', 'Enable')
                    ->first();
                $departmentid = $defaultDepartment->departmentid ?? null;
            }
            
            // Get default shipment if not provided
            $defaultShipment = DB::table('stoma_productshipment')
                ->where('storeid', $storeid)
                ->first();
            $shipmentid = $defaultShipment->shipmentid ?? null;
            
            // Get default supplier if not provided
            $defaultSupplier = StoreSupplier::where('storeid', $storeid)
                ->where('status', 'Enable')
                ->first();
            $supplierid = $defaultSupplier->supplierid ?? null;
            
            if (!$departmentid) {
                return redirect()->back()->with('error', 'No department found. Please create a department first.');
            }
            
            if (!$shipmentid) {
                return redirect()->back()->with('error', 'No shipment method found. Please create a shipment method first.');
            }
            
            if (!$supplierid) {
                return redirect()->back()->with('error', 'No supplier found. Please create a supplier first.');
            }
            
            PurchaseOrder::create([
                'storeid' => $storeid,
                'departmentid' => $departmentid,
                'supplierid' => $supplierid,
                'categoryid' => $validated['categoryid'],
                'shipmentid' => $shipmentid,
                'deliverydocketstatus' => 'Yes',
                'deliverynotes' => null,
                'invoicestatus' => $validated['invoicestatus'],
                'invoicenumber' => $validated['invoicenumber'],
                'total_amount' => $validated['total_amount'],
                'total_tax' => $validated['total_tax'],
                'amount_inc_tax' => $validated['amount_inc_tax'],
                'products_bought' => $validated['products_bought'] ?? null,
                'delivery_date' => $validated['delivery_date'],
                'po_note' => null,
                'purchase_orders_type' => 'Manual entry',
                'status' => 'Yes',
                'creditnote' => 'No',
                'creditnotedesc' => null,
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'insertby' => $username,
                'editdate' => now()->setTime(0, 0, 0),
                'editip' => '',
                'editby' => '0',
            ]);
            
            return redirect()->back()->with('success', 'Invoice Added Successfully.');
        }
    }

    /**
     * Display Yearly Chart View.
     */
    public function reportsChartYearly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.reports_chart_yearly');
    }

    /**
     * Display Monthly Chart View.
     */
    public function reportsChartMonthly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.reports_chart_monthly');
    }

    /**
     * Display Weekly Chart View.
     */
    public function reportsChartWeekly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.reports_chart_weekly');
    }

    /**
     * Get Yearly Chart Data (AJAX).
     */
    public function getAllReportsChartYearly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->select(
                DB::raw('YEAR(delivery_date) as myyear'),
                DB::raw('SUM(amount_inc_tax) as mytotal_amount')
            )
            ->groupBy('myyear')
            ->orderBy('myyear', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'myyear' => (int)$item->myyear,
                    'mytotal_amount' => (float)$item->mytotal_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Get Monthly Chart Data (AJAX).
     */
    public function getAllReportsChartMonthly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->select(
                DB::raw('MONTH(delivery_date) as month'),
                DB::raw('YEAR(delivery_date) as year'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->groupBy('month', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'month' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'year' => $item->year,
                    'total_amount' => (float)$item->total_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Get Weekly Chart Data (AJAX).
     */
    public function getAllPoChartWeekly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->select(
                DB::raw('WEEK(delivery_date) as week'),
                DB::raw('YEAR(delivery_date) as year'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->groupBy('week', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('week', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'week' => (int)$item->week,
                    'year' => (int)$item->year,
                    'total_amount' => (float)$item->total_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Display PO Reports - Yearly Chart View.
     */
    public function poChartYearly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.po_chart_yearly');
    }

    /**
     * Display PO Reports - Monthly Chart View.
     */
    public function poChartMonthly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.po_chart_monthly');
    }

    /**
     * Display PO Reports - Weekly Chart View.
     */
    public function poChartWeekly(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.ordering.po_chart_weekly');
    }

    /**
     * Get PO Reports - Yearly Chart Data (AJAX).
     */
    public function getPoChartYearly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->where('purchase_orders_type', 'Purchase order')
            ->select(
                DB::raw('YEAR(insertdate) as myyear'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->groupBy('myyear')
            ->orderBy('myyear', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'myyear' => (int)$item->myyear,
                    'total_amount' => (float)$item->total_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Get PO Reports - Monthly Chart Data (AJAX).
     */
    public function getPoChartMonthly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->where('purchase_orders_type', 'Purchase order')
            ->select(
                DB::raw('MONTH(delivery_date) as month'),
                DB::raw('YEAR(delivery_date) as year'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->groupBy('month', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get()
            ->map(function($item) {
                $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                              'July', 'August', 'September', 'October', 'November', 'December'];
                return [
                    'month' => $monthNames[(int)$item->month] ?? date('F', mktime(0, 0, 0, $item->month, 1)),
                    'year' => $item->year,
                    'total_amount' => (float)$item->total_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Get PO Reports - Weekly Chart Data (AJAX).
     */
    public function getPoChartWeekly()
    {
        $storeid = $this->getStoreId();
        
        $data = PurchaseOrder::where('storeid', $storeid)
            ->where('purchase_orders_type', 'Purchase order')
            ->select(
                DB::raw('WEEK(delivery_date) as week'),
                DB::raw('YEAR(delivery_date) as year'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->groupBy('week', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('week', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'week' => (string)$item->week, // String format to match CI
                    'year' => (int)$item->year,
                    'total_amount' => (float)$item->total_amount
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Display Supplier Documents index page.
     */
    public function indexSupplierDoc(Request $request): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Fetch all supplier documents for client-side pagination/search/sort
        $supplierDocs = SupplierDocument::with(['supplier'])
            ->where('storeid', $storeid)
            ->orderBy('doc_date', 'DESC')
            ->orderBy('docid', 'DESC')
            ->get();
        
        return view('storeowner.ordering.index_supplier_doc', compact('supplierDocs'));
    }

    /**
     * Display Add Supplier Document form.
     */
    public function addSupplierDoc(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $storeSuppliers = StoreSupplier::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->orderBy('supplier_name', 'ASC')
            ->get();
        
        $supplierDocTypes = SupplierDocType::where('storeid', $storeid)
            ->orderBy('docs_type_name', 'ASC')
            ->get();
        
        return view('storeowner.ordering.add_supplier_doc', compact('storeSuppliers', 'supplierDocTypes'));
    }

    /**
     * Store a new supplier document.
     */
    public function updateSupplierDoc(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $username = $user->username ?? '';
        
        $validated = $request->validate([
            'supplierid' => 'required|integer',
            'docs_type_id' => 'required|integer',
            'docname' => 'required|string|max:255',
            'doc_date' => 'required|date',
            'doc' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ]);
        
        if ($request->hasFile('doc')) {
            $file = $request->file('doc');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . uniqid() . '.' . $extension;
            
            // Store file in storage/app/public/supplierdocuments
            $path = $file->storeAs('supplierdocuments', $fileName, 'public');
            
            SupplierDocument::create([
                'storeid' => $storeid,
                'supplierid' => $validated['supplierid'],
                'doctypeid' => $validated['docs_type_id'],
                'docname' => $validated['docname'],
                'docpath' => $fileName,
                'doc_date' => $validated['doc_date'],
                'insertdatetime' => now(),
                'insertip' => $request->ip(),
                'editdatetime' => now()->setTime(0, 0, 0),
                'editip' => '',
                'status' => 'Enable',
            ]);
            
            return redirect()->route('storeowner.ordering.index_supplier_doc')
                ->with('success', 'Document Added Successfully.');
        }
        
        return redirect()->back()
            ->with('error', 'Please upload a document file.')
            ->withInput();
    }

    /**
     * Delete a supplier document.
     */
    public function deleteSupplierDoc($docid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        try {
            $docid = base64_decode($docid);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid document ID');
        }
        
        $document = SupplierDocument::where('docid', $docid)
            ->where('storeid', $storeid)
            ->first();
        
        if (!$document) {
            return redirect()->route('storeowner.ordering.index_supplier_doc')
                ->with('error', 'Document not found.');
        }
        
        // Delete file from storage
        $filePath = 'supplierdocuments/' . $document->docpath;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        // Delete record
        $document->delete();
        
        return redirect()->route('storeowner.ordering.index_supplier_doc')
            ->with('success', 'Record has been deleted successfully');
    }

    /**
     * Get documents by supplier ID (AJAX).
     */
    public function getDocuments(Request $request)
    {
        $supplierid = $request->input('supplierid');
        
        $documents = SupplierDocument::with(['documentType'])
            ->where('supplierid', $supplierid)
            ->orderBy('doc_date', 'DESC')
            ->get();
        
        return response()->json($documents);
    }

    /**
     * Delete a purchase order.
     */
    public function deletePurchaseOrder($purchase_orders_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        try {
            $purchase_orders_id = base64_decode($purchase_orders_id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid purchase order ID');
        }
        
        try {
            // Delete purchased products first (due to foreign key constraint)
            PurchasedProduct::where('purchase_orders_id', $purchase_orders_id)
                ->where('storeid', $storeid)
                ->delete();
            
            // Delete purchase order
            $deleted = PurchaseOrder::where('purchase_orders_id', $purchase_orders_id)
                ->where('storeid', $storeid)
                ->delete();
            
            if ($deleted) {
                return redirect()->route('storeowner.ordering.report')
                    ->with('success', 'Purchase order has been deleted successfully');
            } else {
                return redirect()->route('storeowner.ordering.report')
                    ->with('error', 'Purchase order not found or could not be deleted');
            }
        } catch (\Exception $e) {
            return redirect()->route('storeowner.ordering.report')
                ->with('error', 'Error deleting purchase order: ' . $e->getMessage());
        }
    }
}

