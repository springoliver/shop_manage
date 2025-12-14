<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PosFloorSection;
use App\Models\PosFloorTable;
use App\Models\PosReceiptPrinter;
use App\Models\PosSalesType;
use App\Models\PosPaymentType;
use App\Models\PosRefundReason;
use App\Models\PosGratuity;
use App\Models\PosDiscount;
use App\Models\PosModifier;
use App\Models\UserGroup;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PosSettingController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Point Of Sale module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Point Of Sale')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display POS Settings index.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $sections = PosFloorSection::where('storeid', $storeid)
            ->orderBy('pos_section_list_number', 'ASC')
            ->get();
        
        return view('storeowner.possetting.sections', compact('sections'));
    }

    /**
     * Display a listing of floor sections.
     */
    public function sections(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $sections = PosFloorSection::where('storeid', $storeid)
            ->orderBy('pos_section_list_number', 'ASC')
            ->get();
        
        return view('storeowner.possetting.sections', compact('sections'));
    }

    /**
     * Show the form for editing a floor section.
     */
    public function editSection($pos_floor_section_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_floor_section_id = base64_decode($pos_floor_section_id);
        $section = PosFloorSection::findOrFail($pos_floor_section_id);
        
        return view('storeowner.possetting.edit_section', compact('section'));
    }

    /**
     * Store or update a floor section.
     */
    public function updateFloorSections(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_floor_section_id') && !empty($request->pos_floor_section_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_floor_section_id' => 'required|string',
                'pos_floor_section_name' => 'required|string|max:255',
                'pos_floor_section_colour' => 'required|string|max:11',
                'pos_section_list_number' => 'required|string|max:10',
            ]);
            
            $pos_floor_section_id = base64_decode($validated['pos_floor_section_id']);
            $section = PosFloorSection::findOrFail($pos_floor_section_id);
            
            $section->pos_floor_section_name = $validated['pos_floor_section_name'];
            $section->pos_floor_section_colour = strtoupper($validated['pos_floor_section_colour']);
            $section->pos_section_list_number = $validated['pos_section_list_number'];
            $section->editdate = now();
            $section->editip = $request->ip();
            $section->save();
            
            $message = 'Section Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_floor_section_name' => 'required|string|max:255',
                'pos_floor_section_colour' => 'required|string|max:11',
                'pos_section_list_number' => 'required|string|max:10',
            ]);
            
            PosFloorSection::create([
                'storeid' => $storeid,
                'pos_floor_section_name' => $validated['pos_floor_section_name'],
                'pos_floor_section_colour' => strtoupper($validated['pos_floor_section_colour']),
                'pos_section_list_number' => $validated['pos_section_list_number'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'insertby' => 0,
            ]);
            
            $message = 'Section Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.sections')
            ->with('success', $message);
    }

    /**
     * Remove the specified floor section.
     */
    public function deleteSection($pos_floor_section_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_floor_section_id = base64_decode($pos_floor_section_id);
        $section = PosFloorSection::findOrFail($pos_floor_section_id);
        $section->delete();
        
        return redirect()->route('storeowner.possetting.sections')
            ->with('success', 'Section has been deleted successfully');
    }

    /**
     * Display a listing of floor tables.
     */
    public function tables(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $sections = PosFloorSection::where('storeid', $storeid)->get();
        
        $tables = DB::table('stoma_pos_floor_tables as pt')
            ->select('pt.*', 'ps.pos_floor_section_name')
            ->leftJoin('stoma_pos_floor_sections as ps', 'ps.pos_floor_section_id', '=', 'pt.pos_floor_section_id')
            ->where('pt.storeid', $storeid)
            ->orderBy('pt.pos_floor_table_id', 'DESC')
            ->get();
        
        return view('storeowner.possetting.tables', compact('sections', 'tables'));
    }

    /**
     * Show the form for editing a floor table.
     */
    public function editTable($pos_floor_table_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_floor_table_id = base64_decode($pos_floor_table_id);
        $table = PosFloorTable::findOrFail($pos_floor_table_id);
        
        $storeid = $this->getStoreId();
        
        $sections = PosFloorSection::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.edit_table', compact('table', 'sections'));
    }

    /**
     * Store or update a floor table.
     */
    public function updateFloorTables(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_floor_table_id') && !empty($request->pos_floor_table_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_floor_table_id' => 'required|string',
                'pos_floor_section_id' => 'required|string',
                'pos_floor_table_number' => 'required|string|max:11',
                'pos_floor_table_seat' => 'required|string|max:11',
                'pos_floor_table_colour' => 'required|string|max:11',
            ]);
            
            $pos_floor_table_id = base64_decode($validated['pos_floor_table_id']);
            $table = PosFloorTable::findOrFail($pos_floor_table_id);
            
            $table->pos_floor_section_id = $validated['pos_floor_section_id'];
            $table->pos_floor_table_number = $validated['pos_floor_table_number'];
            $table->pos_floor_table_seat = $validated['pos_floor_table_seat'];
            $table->pos_floor_table_colour = strtoupper($validated['pos_floor_table_colour']);
            $table->editdate = now();
            $table->editip = $request->ip();
            $table->save();
            
            $message = 'Table Updated Successfully.';
            $redirectRoute = 'storeowner.possetting.tables';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_floor_section_id' => 'required|string',
                'pos_floor_table_number' => 'required|string|max:11',
                'pos_floor_table_seat' => 'required|string|max:11',
                'pos_floor_table_colour' => 'required|string|max:11',
            ]);
            
            PosFloorTable::create([
                'storeid' => $storeid,
                'pos_floor_section_id' => $validated['pos_floor_section_id'],
                'pos_floor_table_number' => $validated['pos_floor_table_number'],
                'pos_floor_table_seat' => $validated['pos_floor_table_seat'],
                'pos_floor_table_colour' => strtoupper($validated['pos_floor_table_colour']),
                'pos_floor_table_width' => 0,
                'pos_floor_table_height' => 0,
                'pos_floor_table_top' => 0,
                'pos_floor_table_left' => 0,
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'insertby' => 0,
            ]);
            
            $message = 'Table Added Successfully.';
            $redirectRoute = 'storeowner.possetting.tables';
        }
        
        return redirect()->route($redirectRoute)
            ->with('success', $message);
    }

    /**
     * Remove the specified floor table.
     */
    public function deleteTable($pos_floor_table_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_floor_table_id = base64_decode($pos_floor_table_id);
        $table = PosFloorTable::findOrFail($pos_floor_table_id);
        $table->delete();
        
        return redirect()->route('storeowner.possetting.tables')
            ->with('success', 'Table has been deleted successfully');
    }

    /**
     * Display a listing of receipt printers.
     */
    public function printers(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $printers = PosReceiptPrinter::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.printers', compact('printers'));
    }

    /**
     * Show the form for editing a printer.
     */
    public function editPrinter($pos_receiptprinters_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_receiptprinters_id = base64_decode($pos_receiptprinters_id);
        $printer = PosReceiptPrinter::findOrFail($pos_receiptprinters_id);
        
        return view('storeowner.possetting.edit_printer', compact('printer'));
    }

    /**
     * Store or update a printer.
     */
    public function updatePrinters(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_receiptprinters_id') && !empty($request->pos_receiptprinters_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_receiptprinters_id' => 'required|string',
                'pos_receiptprinters_name' => 'required|string|max:50',
                'pos_receiptprinters_ipadress' => 'required|string|max:51',
                'pos_receiptprinters_port' => 'required|string|max:22',
                'pos_receiptprinters_type' => 'required|string|in:network,windows,linux',
                'pos_receiptprinters_profile' => 'required|string|max:11',
                'pos_receiptprinters_path' => 'nullable|string|max:255',
                'pos_receiptprinters_char_per_line' => 'required|string|max:11',
                'table_number' => 'nullable|in:Enable,Disable',
                'customer_number' => 'nullable|in:Enable,Disable',
                'server_name' => 'nullable|in:Enable,Disable',
                'receipt_number' => 'nullable|in:Enable,Disable',
                'store_name' => 'nullable|in:Enable,Disable',
                'date_time' => 'nullable|in:Enable,Disable',
                'tax_summary' => 'nullable|in:Enable,Disable',
                'tender_details' => 'nullable|in:Enable,Disable',
                'customer_address' => 'nullable|in:Enable,Disable',
                'customer_email' => 'nullable|in:Enable,Disable',
                'customer_tel' => 'nullable|in:Enable,Disable',
                'service_charge' => 'nullable|in:Enable,Disable',
                'sc_message' => 'nullable|string|max:250',
                'cut_paper' => 'nullable|in:Enable,Disable',
                'barcode' => 'nullable|in:Enable,Disable',
            ]);
            
            $pos_receiptprinters_id = base64_decode($validated['pos_receiptprinters_id']);
            $printer = PosReceiptPrinter::findOrFail($pos_receiptprinters_id);
            
            $printer->pos_receiptprinters_name = $validated['pos_receiptprinters_name'];
            $printer->pos_receiptprinters_ipadress = $validated['pos_receiptprinters_ipadress'];
            $printer->pos_receiptprinters_port = $validated['pos_receiptprinters_port'];
            $printer->pos_receiptprinters_type = $validated['pos_receiptprinters_type'];
            $printer->pos_receiptprinters_profile = $validated['pos_receiptprinters_profile'];
            $printer->pos_receiptprinters_path = $validated['pos_receiptprinters_path'] ?? '';
            $printer->pos_receiptprinters_char_per_line = $validated['pos_receiptprinters_char_per_line'];
            $printer->table_number = $validated['table_number'] ?? 'Disable';
            $printer->customer_number = $validated['customer_number'] ?? 'Disable';
            $printer->server_name = $validated['server_name'] ?? 'Disable';
            $printer->receipt_number = $validated['receipt_number'] ?? 'Enable';
            $printer->store_name = $validated['store_name'] ?? 'Enable';
            $printer->date_time = $validated['date_time'] ?? 'Enable';
            $printer->tax_summary = $validated['tax_summary'] ?? 'Disable';
            $printer->tender_details = $validated['tender_details'] ?? 'Disable';
            $printer->customer_address = $validated['customer_address'] ?? 'Disable';
            $printer->customer_email = $validated['customer_email'] ?? 'Disable';
            $printer->customer_tel = $validated['customer_tel'] ?? 'Disable';
            $printer->service_charge = $validated['service_charge'] ?? 'Enable';
            $printer->sc_message = $validated['sc_message'] ?? '';
            $printer->cut_paper = $validated['cut_paper'] ?? 'Enable';
            $printer->barcode = $validated['barcode'] ?? 'Disable';
            $printer->editdate = now();
            $printer->editip = $request->ip();
            $printer->save();
            
            $message = 'Printer Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_receiptprinters_name' => 'required|string|max:50',
                'pos_receiptprinters_ipadress' => 'required|string|max:51',
                'pos_receiptprinters_port' => 'required|string|max:22',
                'pos_receiptprinters_type' => 'required|string|in:network,windows,linux',
                'pos_receiptprinters_profile' => 'required|string',
                'pos_receiptprinters_path' => 'nullable|string',
                'pos_receiptprinters_char_per_line' => 'required|string',
            ]);
            
            PosReceiptPrinter::create([
                'storeid' => $storeid,
                'pos_receiptprinters_name' => $validated['pos_receiptprinters_name'],
                'pos_receiptprinters_ipadress' => $validated['pos_receiptprinters_ipadress'],
                'pos_receiptprinters_port' => $validated['pos_receiptprinters_port'],
                'pos_receiptprinters_type' => $validated['pos_receiptprinters_type'],
                'pos_receiptprinters_profile' => $validated['pos_receiptprinters_profile'],
                'pos_receiptprinters_path' => $validated['pos_receiptprinters_path'] ?? '',
                'pos_receiptprinters_char_per_line' => $validated['pos_receiptprinters_char_per_line'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
                'insertby' => '',
                'sc_message' => '', 
            ]);
            
            $message = 'Printer Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.printers')
            ->with('success', $message);
    }

    /**
     * Remove the specified printer.
     */
    public function deletePrinter($pos_receiptprinters_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_receiptprinters_id = base64_decode($pos_receiptprinters_id);
        $printer = PosReceiptPrinter::findOrFail($pos_receiptprinters_id);
        $printer->delete();
        
        return redirect()->route('storeowner.possetting.printers')
            ->with('success', 'Printer has been deleted successfully');
    }

    /**
     * Display a listing of sales types.
     */
    public function salesTypes(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $salesTypes = PosSalesType::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.sales_types', compact('salesTypes'));
    }

    /**
     * Show the form for editing a sales type.
     */
    public function editSalesType($pos_sales_types_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_sales_types_id = base64_decode($pos_sales_types_id);
        $salesType = PosSalesType::findOrFail($pos_sales_types_id);
        
        return view('storeowner.possetting.edit_sales_type', compact('salesType'));
    }

    /**
     * Store or update a sales type.
     */
    public function updateSalesTypes(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_sales_types_id') && !empty($request->pos_sales_types_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_sales_types_id' => 'required|string',
                'pos_sales_type_name' => 'required|string|max:51',
            ]);
            
            $pos_sales_types_id = base64_decode($validated['pos_sales_types_id']);
            $salesType = PosSalesType::findOrFail($pos_sales_types_id);
            
            $salesType->pos_sales_type_name = $validated['pos_sales_type_name'];
            $salesType->editdate = now();
            $salesType->editip = $request->ip();
            $salesType->save();
            
            $message = 'Sales Type Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_sales_type_name' => 'required|string|max:51',
            ]);
            
            PosSalesType::create([
                'storeid' => $storeid,
                'pos_sales_type_name' => $validated['pos_sales_type_name'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Sales Type Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.sales-types')
            ->with('success', $message);
    }

    /**
     * Remove the specified sales type.
     */
    public function deleteSalesType($pos_sales_types_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_sales_types_id = base64_decode($pos_sales_types_id);
        $salesType = PosSalesType::findOrFail($pos_sales_types_id);
        $salesType->delete();
        
        return redirect()->route('storeowner.possetting.sales-types')
            ->with('success', 'Sales Type has been deleted successfully');
    }

    /**
     * Display a listing of payment types.
     */
    public function paymentTypes(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $paymentTypes = PosPaymentType::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.payment_types', compact('paymentTypes'));
    }

    /**
     * Show the form for editing a payment type.
     */
    public function editPaymentType($pos_payment_types_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_payment_types_id = base64_decode($pos_payment_types_id);
        $paymentType = PosPaymentType::findOrFail($pos_payment_types_id);
        
        return view('storeowner.possetting.edit_payment_type', compact('paymentType'));
    }

    /**
     * Store or update a payment type.
     */
    public function updatePaymentTypes(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_payment_types_id') && !empty($request->pos_payment_types_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_payment_types_id' => 'required|string',
                'pos_payment_type_name' => 'required|string|max:50',
            ]);
            
            $pos_payment_types_id = base64_decode($validated['pos_payment_types_id']);
            $paymentType = PosPaymentType::findOrFail($pos_payment_types_id);
            
            $paymentType->pos_payment_type_name = $validated['pos_payment_type_name'];
            $paymentType->editdate = now();
            $paymentType->editip = $request->ip();
            $paymentType->save();
            
            $message = 'Payment Type Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_payment_type_name' => 'required|string|max:50',
            ]);
            
            PosPaymentType::create([
                'storeid' => $storeid,
                'pos_payment_type_name' => $validated['pos_payment_type_name'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Payment Type Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.payment-types')
            ->with('success', $message);
    }

    /**
     * Remove the specified payment type.
     */
    public function deletePaymentType($pos_payment_types_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_payment_types_id = base64_decode($pos_payment_types_id);
        $paymentType = PosPaymentType::findOrFail($pos_payment_types_id);
        $paymentType->delete();
        
        return redirect()->route('storeowner.possetting.payment-types')
            ->with('success', 'Payment Type has been deleted successfully');
    }

    /**
     * Display a listing of refund reasons.
     */
    public function refundReasons(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $refundReasons = DB::table('stoma_pos_refund_reasons as pr')
            ->select('pr.*', 'u.groupname')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'pr.min_security_level_id')
            ->where('pr.storeid', $storeid)
            ->get();
        
        // Get user groups for dropdown
        $userGroups = DB::table('stoma_store_usergroup as su')
            ->join('stoma_usergroup as u', 'u.usergroupid', '=', 'su.usergroupid')
            ->where('su.storeid', $storeid)
            ->select('u.usergroupid', 'u.groupname')
            ->groupBy('u.usergroupid', 'u.groupname')
            ->get();
        
        return view('storeowner.possetting.refund_reasons', compact('refundReasons', 'userGroups'));
    }

    /**
     * Show the form for editing a refund reason.
     */
    public function editRefundReason($pos_refund_reason_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_refund_reason_id = base64_decode($pos_refund_reason_id);
        $refundReason = PosRefundReason::findOrFail($pos_refund_reason_id);
        
        $storeid = $this->getStoreId();
        
        // Get user groups for dropdown
        $userGroups = DB::table('stoma_store_usergroup as su')
            ->join('stoma_usergroup as u', 'u.usergroupid', '=', 'su.usergroupid')
            ->where('su.storeid', $storeid)
            ->select('u.usergroupid', 'u.groupname')
            ->groupBy('u.usergroupid', 'u.groupname')
            ->get();
        
        return view('storeowner.possetting.edit_refund_reason', compact('refundReason', 'userGroups'));
    }

    /**
     * Store or update a refund reason.
     */
    public function updateRefundReasons(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_refund_reason_id') && !empty($request->pos_refund_reason_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_refund_reason_id' => 'required|string',
                'pos_refund_reason_name' => 'required|string|max:50',
                'min_security_level_id' => 'required|integer|exists:stoma_usergroup,usergroupid',
            ]);
            
            $pos_refund_reason_id = base64_decode($validated['pos_refund_reason_id']);
            $refundReason = PosRefundReason::findOrFail($pos_refund_reason_id);
            
            $refundReason->pos_refund_reason_name = $validated['pos_refund_reason_name'];
            $refundReason->min_security_level_id = $validated['min_security_level_id'];
            $refundReason->editdate = now();
            $refundReason->editip = $request->ip();
            $refundReason->save();
            
            $message = 'Refund Reason Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_refund_reason_name' => 'required|string|max:50',
                'min_security_level_id' => 'required|integer|exists:stoma_usergroup,usergroupid',
            ]);
            
            PosRefundReason::create([
                'storeid' => $storeid,
                'pos_refund_reason_name' => $validated['pos_refund_reason_name'],
                'min_security_level_id' => $validated['min_security_level_id'],
                'insertip' => $request->ip(),
                'isertdate' => now(),
            ]);
            
            $message = 'Refund Reason Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.refund-reasons')
            ->with('success', $message);
    }

    /**
     * Remove the specified refund reason.
     */
    public function deleteRefundReason($pos_refund_reason_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_refund_reason_id = base64_decode($pos_refund_reason_id);
        $refundReason = PosRefundReason::findOrFail($pos_refund_reason_id);
        $refundReason->delete();
        
        return redirect()->route('storeowner.possetting.refund-reasons')
            ->with('success', 'Refund Reason has been deleted successfully');
    }

    /**
     * Display a listing of gratuity.
     */
    public function gratuity(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $gratuities = PosGratuity::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.gratuity', compact('gratuities'));
    }

    /**
     * Show the form for editing a gratuity.
     */
    public function editGratuity($pos_graduity_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_graduity_id = base64_decode($pos_graduity_id);
        $gratuity = PosGratuity::findOrFail($pos_graduity_id);
        
        return view('storeowner.possetting.edit_gratuity', compact('gratuity'));
    }

    /**
     * Store or update a gratuity.
     */
    public function updateGratuity(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_graduity_id') && !empty($request->pos_graduity_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_graduity_id' => 'required|string',
                'pos_graduity_percentage' => 'required|string|max:11',
                'pos_graduity_customers_over' => 'required|string|max:11',
            ]);
            
            $pos_graduity_id = base64_decode($validated['pos_graduity_id']);
            $gratuity = PosGratuity::findOrFail($pos_graduity_id);
            
            $gratuity->pos_graduity_percentage = $validated['pos_graduity_percentage'];
            $gratuity->pos_graduity_customers_over = $validated['pos_graduity_customers_over'];
            $gratuity->editdate = now();
            $gratuity->editip = $request->ip();
            $gratuity->save();
            
            $message = 'Gratuity Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_graduity_percentage' => 'required|string|max:11',
                'pos_graduity_customers_over' => 'required|string|max:11',
            ]);
            
            PosGratuity::create([
                'storeid' => $storeid,
                'pos_graduity_percentage' => $validated['pos_graduity_percentage'],
                'pos_graduity_customers_over' => $validated['pos_graduity_customers_over'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Gratuity Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.gratuity')
            ->with('success', $message);
    }

    /**
     * Remove the specified gratuity.
     */
    public function deleteGratuity($pos_graduity_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_graduity_id = base64_decode($pos_graduity_id);
        $gratuity = PosGratuity::findOrFail($pos_graduity_id);
        $gratuity->delete();
        
        return redirect()->route('storeowner.possetting.gratuity')
            ->with('success', 'Gratuity has been deleted successfully');
    }

    /**
     * Display a listing of discounts.
     */
    public function discounts(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $discounts = PosDiscount::where('storeid', $storeid)->get();
        
        return view('storeowner.possetting.discounts', compact('discounts'));
    }

    /**
     * Show the form for editing a discount.
     */
    public function editDiscount($pos_discount_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_discount_id = base64_decode($pos_discount_id);
        $discount = PosDiscount::findOrFail($pos_discount_id);
        
        return view('storeowner.possetting.edit_discount', compact('discount'));
    }

    /**
     * Store or update a discount.
     */
    public function updateDiscount(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_discount_id') && !empty($request->pos_discount_id)) {
            // Update existing
            $validated = $request->validate([
                'pos_discount_id' => 'required|string',
                'pos_discount_percentage' => 'required|string|max:11',
                'pos_discount_name' => 'required|string|max:50',
            ]);
            
            $pos_discount_id = base64_decode($validated['pos_discount_id']);
            $discount = PosDiscount::findOrFail($pos_discount_id);
            
            $discount->pos_discount_percentage = $validated['pos_discount_percentage'];
            $discount->pos_discount_name = $validated['pos_discount_name'];
            $discount->editdate = now();
            $discount->editip = $request->ip();
            $discount->save();
            
            $message = 'Discount Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'pos_discount_percentage' => 'required|string|max:11',
                'pos_discount_name' => 'required|string|max:50',
            ]);
            
            PosDiscount::create([
                'storeid' => $storeid,
                'pos_discount_percentage' => $validated['pos_discount_percentage'],
                'pos_discount_name' => $validated['pos_discount_name'],
                'insertdate' => now(),
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Discount Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.discounts')
            ->with('success', $message);
    }

    /**
     * Remove the specified discount.
     */
    public function deleteDiscount($pos_discount_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_discount_id = base64_decode($pos_discount_id);
        $discount = PosDiscount::findOrFail($pos_discount_id);
        $discount->delete();
        
        return redirect()->route('storeowner.possetting.discounts')
            ->with('success', 'Discount has been deleted successfully');
    }

    /**
     * Display a listing of modifiers.
     */
    public function modifiers(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get modifiers with product group names if table exists
        $modifiers = PosModifier::where('storeid', $storeid)->get();
        
        // If catalog_product_group table exists, join to get group names
        if (Schema::hasTable('stoma_catalog_product_group')) {
            $modifiers = DB::table('stoma_pos_modifiers as pm')
                ->select('pm.*', 'cpg.catalog_product_group_name')
                ->leftJoin('stoma_catalog_product_group as cpg', 'cpg.catalog_product_groupid', '=', 'pm.catalog_product_groupid')
                ->where('pm.storeid', $storeid)
                ->get();
        } else {
            // If table doesn't exist, just get modifiers without join
            $modifiers = $modifiers->map(function($modifier) {
                return (object) [
                    'pos_modifiers_id' => $modifier->pos_modifiers_id,
                    'pos_modifier_name' => $modifier->pos_modifier_name,
                    'catalog_product_groupid' => $modifier->catalog_product_groupid,
                    'catalog_product_group_name' => null,
                ];
            });
        }
        
        // Get catalog product groups for dropdown
        $productGroups = [];
        if (Schema::hasTable('stoma_catalog_product_group')) {
            $productGroups = DB::table('stoma_catalog_product_group')
                ->where('storeid', $storeid)
                ->get();
        }
        
        return view('storeowner.possetting.modifiers', compact('modifiers', 'productGroups'));
    }

    /**
     * Show the form for editing a modifier.
     */
    public function editModifier($pos_modifiers_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_modifiers_id = base64_decode($pos_modifiers_id);
        $modifier = PosModifier::findOrFail($pos_modifiers_id);
        
        $storeid = $this->getStoreId();
        
        // Get catalog product groups for dropdown
        $productGroups = [];
        if (Schema::hasTable('stoma_catalog_product_group')) {
            $productGroups = DB::table('stoma_catalog_product_group')
                ->where('storeid', $storeid)
                ->get();
        }
        
        return view('storeowner.possetting.edit_modifier', compact('modifier', 'productGroups'));
    }

    /**
     * Store or update a modifier.
     */
    public function updateModifier(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($request->has('pos_modifiers_id') && !empty($request->pos_modifiers_id)) {
            // Update existing
            $rules = [
                'pos_modifiers_id' => 'required|string',
                'pos_modifier_name' => 'required|string|max:50',
                'catalog_product_groupid' => 'required|integer',
            ];
            
            // Only validate against table if it exists
            if (Schema::hasTable('stoma_catalog_product_group')) {
                $rules['catalog_product_groupid'] .= '|exists:stoma_catalog_product_group,catalog_product_groupid';
            }
            
            $validated = $request->validate($rules);
            
            $pos_modifiers_id = base64_decode($validated['pos_modifiers_id']);
            $modifier = PosModifier::findOrFail($pos_modifiers_id);
            
            $modifier->pos_modifier_name = $validated['pos_modifier_name'];
            $modifier->catalog_product_groupid = $validated['catalog_product_groupid'];
            $modifier->editdate = now();
            $modifier->editip = $request->ip();
            $modifier->save();
            
            $message = 'Modifier Updated Successfully.';
        } else {
            // Create new
            $rules = [
                'pos_modifier_name' => 'required|string|max:50',
                'catalog_product_groupid' => 'required|integer',
            ];
            
            // Only validate against table if it exists
            if (Schema::hasTable('stoma_catalog_product_group')) {
                $rules['catalog_product_groupid'] .= '|exists:stoma_catalog_product_group,catalog_product_groupid';
            }
            
            $validated = $request->validate($rules);
            
            PosModifier::create([
                'storeid' => $storeid,
                'pos_modifier_name' => $validated['pos_modifier_name'],
                'catalog_product_groupid' => $validated['catalog_product_groupid'],
                'insertip' => $request->ip(),
                'isertdate' => now(),
            ]);
            
            $message = 'Modifier Added Successfully.';
        }
        
        return redirect()->route('storeowner.possetting.modifiers')
            ->with('success', $message);
    }

    /**
     * Remove the specified modifier.
     */
    public function deleteModifier($pos_modifiers_id): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $pos_modifiers_id = base64_decode($pos_modifiers_id);
        $modifier = PosModifier::findOrFail($pos_modifiers_id);
        $modifier->delete();
        
        return redirect()->route('storeowner.possetting.modifiers')
            ->with('success', 'Modifier has been deleted successfully');
    }

    /**
     * Display floor layout.
     */
    public function floorLayout(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $sections = PosFloorSection::where('storeid', $storeid)
            ->orderBy('pos_section_list_number', 'ASC')
            ->get();
        
        $tables = PosFloorTable::where('storeid', $storeid)
            ->orderBy('pos_floor_table_id', 'ASC')
            ->get();
        
        return view('storeowner.possetting.floor_layout', compact('sections', 'tables'));
    }

    /**
     * Display floor layout filtered by section.
     */
    public function floorLayoutSectionsTables($pos_floor_section_id): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $pos_floor_section_id = base64_decode($pos_floor_section_id);
        
        $sections = PosFloorSection::where('storeid', $storeid)
            ->orderBy('pos_section_list_number', 'ASC')
            ->get();
        
        $tables = PosFloorTable::where('storeid', $storeid)
            ->where('pos_floor_section_id', $pos_floor_section_id)
            ->orderBy('pos_floor_table_id', 'ASC')
            ->get();
        
        return view('storeowner.possetting.floor_layout', compact('sections', 'tables'));
    }
}

