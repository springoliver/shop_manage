<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchasedProduct;
use App\Models\StoreProduct;
use App\Models\PurchaseMeasure;
use App\Models\TaxSetting;
use App\Models\Roster;
use App\Models\WeekRoster;
use App\Models\Week;
use App\Models\Year;
use App\Models\StoreEmployee;
use App\Models\Department;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AjaxController extends Controller
{
    use HandlesEmployeeAccess;
    /**
     * Get products by supplier ID.
     */
    public function getProductsBySupplierId(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        
        if (!$supplierId) {
            return Response::json(['data' => []]);
        }
        
        $storeid = $this->getStoreId();
        
        $products = DB::table('stoma_store_products as sp')
            ->select(
                'sp.productid',
                'sp.product_name',
                'sp.product_price',
                'sp.supplierid',
                'sp.departmentid',
                'sp.shipmentid',
                'sp.purchasemeasuresid',
                'ts.taxid',
                'ts.tax_name',
                'ts.tax_amount',
                'pm.purchasemeasure'
            )
            ->leftJoin('stoma_tax_settings as ts', 'sp.taxid', '=', 'ts.taxid')
            ->leftJoin('stoma_purchasemeasures as pm', 'sp.purchasemeasuresid', '=', 'pm.purchasemeasuresid')
            ->where('sp.storeid', $storeid)
            ->where('sp.product_status', 'Enable')
            ->where(DB::raw('CAST(sp.supplierid AS UNSIGNED)'), $supplierId)
            ->orderBy('sp.product_name', 'asc')
            ->get();
        
        return Response::json(['data' => $products]);
    }
    
    /**
     * Get purchase order details by purchase order ID.
     */
    public function getPurchaseOrderDetail(Request $request)
    {
        $purchaseOrderId = $request->get('purchase_orders_id');
        
        if (!$purchaseOrderId) {
            return Response::json(['purchase_order' => null, 'data' => []]);
        }
        
        $storeid = $this->getStoreId();
        
        // Get purchase order with supplier and store info
        $purchaseOrder = PurchaseOrder::with(['supplier', 'store'])
            ->where('purchase_orders_id', $purchaseOrderId)
            ->where('storeid', $storeid)
            ->first();
        
        if (!$purchaseOrder) {
            return Response::json(['purchase_order' => null, 'data' => []]);
        }
        
        // Get purchased products with related data
        $purchasedProducts = DB::table('stoma_purchasedproducts as pp')
            ->select(
                'pp.productid',
                'pp.quantity',
                'pp.product_price',
                'pp.totalamount',
                'pp.taxid',
                'pp.purchasemeasuresid',
                'sp.product_name',
                'pm.purchasemeasure',
                'ts.tax_name',
                'ts.tax_amount',
                'pp.supplierid',
                'pp.departmentid',
                'pp.shipmentid'
            )
            ->leftJoin('stoma_store_products as sp', 'pp.productid', '=', 'sp.productid')
            ->leftJoin('stoma_purchasemeasures as pm', 'pp.purchasemeasuresid', '=', 'pm.purchasemeasuresid')
            ->leftJoin('stoma_tax_settings as ts', 'sp.taxid', '=', 'ts.taxid')
            ->where('pp.purchase_orders_id', $purchaseOrderId)
            ->get();
        
        // Format purchase order data
        $orderData = [
            'purchase_orders_id' => $purchaseOrder->purchase_orders_id,
            'supplier_name' => $purchaseOrder->supplier->supplier_name ?? '',
            'supplier_email' => $purchaseOrder->supplier->supplier_email ?? '',
            'supplier_phone' => $purchaseOrder->supplier->supplier_phone ?? '',
            'supplier_acc_number' => $purchaseOrder->supplier->supplier_acc_number ?? '',
            'total_amount' => $purchaseOrder->total_amount ?? 0,
            'total_tax' => $purchaseOrder->total_tax ?? 0,
            'amount_inc_tax' => $purchaseOrder->amount_inc_tax ?? 0,
            'insertdate' => $purchaseOrder->insertdate ?? '',
            'store_name' => $purchaseOrder->store->storename ?? '',
            'store_email' => $purchaseOrder->store->store_email ?? '',
        ];
        
        return Response::json([
            'purchase_order' => $orderData,
            'data' => $purchasedProducts
        ]);
    }
    
    /**
     * Remove purchase order by purchase order ID.
     */
    public function removePurchaseOrder(Request $request)
    {
        $purchaseOrderId = $request->get('purchase_orders_id');
        
        if (!$purchaseOrderId) {
            return Response::json(['status' => false]);
        }
        
        $storeid = $this->getStoreId();
        
        try {
            // Delete purchased products first (due to foreign key constraint)
            PurchasedProduct::where('purchase_orders_id', $purchaseOrderId)
                ->where('storeid', $storeid)
                ->delete();
            
            // Delete purchase order
            $deleted = PurchaseOrder::where('purchase_orders_id', $purchaseOrderId)
                ->where('storeid', $storeid)
                ->delete();
            
            return Response::json(['status' => $deleted > 0]);
        } catch (\Exception $e) {
            return Response::json(['status' => false]);
        }
    }

    /**
     * Get roster data for an employee (base roster template).
     */
    public function getRosterData(Request $request)
    {
        $employeeid = $request->get('employeeid');
        
        if (!$employeeid) {
            return Response::json(['error' => 'Employee ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        $rosters = Roster::where('employeeid', $employeeid)
            ->where('storeid', $storeid)
            ->get()
            ->keyBy('day');
        
        $employee = StoreEmployee::find($employeeid);
        
        return view('storeowner.roster.partials.modal_roster', compact('rosters', 'employee'))->render();
    }

    /**
     * Get roster template data (base or weekly roster).
     */
    public function getRosterTemplateData(Request $request)
    {
        $employeeid = $request->get('employeeid');
        $weeknumber = $request->get('weeknumber');
        $modelname = $request->get('modelname', '');
        
        if (!$employeeid) {
            return Response::json(['error' => 'Employee ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        $weekid = null;
        $rosters = collect();
        
        if ($weeknumber) {
            // Get weekly roster
            $date = new \DateTime($weeknumber);
            $weekNum = (int) $date->format('W');
            $year = (int) $date->format('Y');
            
            $yearModel = Year::where('year', $year)->first();
            if ($yearModel) {
                $week = Week::where('weeknumber', $weekNum)
                    ->where('yearid', $yearModel->yearid)
                    ->first();
            } else {
                $week = null;
            }
            
            if ($week) {
                $weekid = $week->weekid;
                $rosters = WeekRoster::where('employeeid', $employeeid)
                    ->where('weekid', $weekid)
                    ->where('storeid', $storeid)
                    ->get()
                    ->keyBy('day');
            }
        } else {
            // Get base roster template
            $rosters = Roster::where('employeeid', $employeeid)
                ->where('storeid', $storeid)
                ->orderByRaw("FIELD(day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
                ->get()
                ->keyBy('day');
        }
        
        $employee = StoreEmployee::find($employeeid);
        
        // Ensure we have 7 days (create empty entries if missing)
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $weekroster = [];
        foreach ($days as $index => $day) {
            if (isset($rosters[$day])) {
                $weekroster[] = $rosters[$day];
            } else {
                // Create empty roster entry for this day
                $weekroster[] = (object) [
                    'day' => $day,
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'work_status' => 'off',
                ];
            }
        }
        
        return view('storeowner.roster.partials.modal_template_edit', compact('employee', 'weekroster', 'modelname'))->render();
    }

    /**
     * Get current week roster data for an employee.
     */
    public function getRosterDatas(Request $request)
    {
        $employeeid = $request->get('employeeid');
        $modelname = $request->get('modelname', '');
        
        if (!$employeeid) {
            return Response::json(['error' => 'Employee ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        $date = date('Y-m-d');
        $weeknumber = (int) date('W');
        $year = (int) date('Y');
        
        $yearModel = Year::where('year', $year)->first();
        if ($yearModel) {
            $week = Week::where('weeknumber', $weeknumber)
                ->where('yearid', $yearModel->yearid)
                ->first();
        } else {
            $week = null;
        }
        
        $weekid = null;
        $rosters = collect();
        
        if ($week) {
            $weekid = $week->weekid;
            $rosters = WeekRoster::where('employeeid', $employeeid)
                ->where('weekid', $weekid)
                ->where('storeid', $storeid)
                ->get()
                ->keyBy('day');
        }
        
        $employee = StoreEmployee::find($employeeid);
        
        return view('storeowner.roster.partials.modal_roster', compact('rosters', 'employee', 'weekid', 'modelname'))->render();
    }

    /**
     * Get edit employee weekly roster data.
     */
    public function getEditEmployeeRoster(Request $request)
    {
        $employeeid = $request->get('employeeid');
        $weekid = $request->get('weekid');
        $modelname = $request->get('modelname', '');
        
        if (!$employeeid || !$weekid) {
            return Response::json(['error' => 'Employee ID and Week ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        // Get rosters keyed by day (matching CI's structure)
        $rostersCollection = WeekRoster::where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->where('storeid', $storeid)
            ->get();
        
        // Create array keyed by day name, with default entries for missing days
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $rosters = [];
        
        foreach ($days as $day) {
            $roster = $rostersCollection->firstWhere('day', $day);
            if ($roster) {
                $rosters[$day] = $roster;
            } else {
                // Create a default entry for missing days
                $rosters[$day] = (object) [
                    'start_time' => '00:00:00',
                    'end_time' => '00:00:00',
                    'work_status' => 'off',
                    'day' => $day
                ];
            }
        }
        
        $employee = StoreEmployee::find($employeeid);
        
        if (!$employee) {
            return Response::json(['error' => 'Employee not found']);
        }
        
        return view('storeowner.roster.partials.modal_search_edit', compact('rosters', 'employee', 'weekid', 'modelname'))->render();
    }

    /**
     * Check employees on leave for a selected week.
     */
    public function checkEmployeeInLeave(Request $request)
    {
        $weeknumber = $request->get('weeknumber');
        
        if (!$weeknumber) {
            return Response::json(['error' => 'Week number required']);
        }
        
        $storeid = $this->getStoreId();
        
        $date = new \DateTime($weeknumber);
        $weekStart = clone $date;
        $weekStart->modify('monday this week');
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        
        $leaves = DB::table('stoma_holiday_request as hr')
            ->join('stoma_employee as e', 'e.employeeid', '=', 'hr.employeeid')
            ->select(
                'e.employeeid',
                'e.firstname',
                'e.lastname',
                DB::raw('DATE(hr.from_date) AS start_date'),
                DB::raw('DATE(hr.to_date) AS end_date')
            )
            ->where('hr.storeid', $storeid)
            ->where('hr.status', 'Approved')
            ->where('e.status', '!=', 'Deactivate')
            ->whereBetween('hr.from_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->orWhere(function($q) use ($storeid, $weekStart, $weekEnd) {
                $q->where('hr.storeid', $storeid)
                  ->where('hr.status', 'Approved')
                  ->whereBetween('hr.to_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
            })
            ->get();
        
        return view('storeowner.roster.partials.modal_leave', compact('leaves', 'weeknumber'))->render();
    }

    /**
     * Check department hour limits (for base roster).
     */
    public function checkDepartmentHour(Request $request)
    {
        $employeeid = $request->get('employeeid');
        $hour = (int) $request->get('hour', 0);
        $minute = (int) $request->get('minute', 0);
        $day_hrs = (float) $request->get('day_hrs', 0);
        
        if (!$employeeid) {
            return Response::json(['error' => 'Employee ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        // Get department max day hours
        $employee = StoreEmployee::with('department')->find($employeeid);
        $maxDayHrs = $employee->department->day_max_time ?? 0;
        
        if ($day_hrs > 0 && $day_hrs > $maxDayHrs) {
            return Response::json(['status' => 'error']);
        }
        
        // Get department max week hours
        $maxWeekHrs = $employee->department->roster_max_time ?? 0;
        
        // Get all employees in same department
        $deptEmployees = StoreEmployee::where('departmentid', $employee->departmentid)
            ->where('storeid', $storeid)
            ->where('status', 'Active')
            ->pluck('employeeid')
            ->toArray();
        
        // Calculate total hours for all employees in department
        $totaltime = 0;
        foreach ($deptEmployees as $empId) {
            if ($empId == $employeeid) continue;
            
            $rosterDetails = Roster::where('employeeid', $empId)
                ->where('storeid', $storeid)
                ->where('work_status', 'on')
                ->get();
            
            foreach ($rosterDetails as $roster) {
                $start = strtotime($roster->start_time);
                $end = strtotime($roster->end_time);
                $diff = round(($end - $start) / 3600, 1);
                $totaltime += $diff;
            }
        }
        
        // Add current employee's hours
        $total = $totaltime + $hour;
        $emp_total = ($total * 60) + $minute;
        $dept_total = $maxWeekHrs * 60;
        
        if ($totaltime > $maxWeekHrs || $emp_total > $dept_total) {
            return Response::json(['status' => 'error']);
        }
        
        return Response::json(['status' => 'success']);
    }

    /**
     * Check department hour limits (for modal/weekly roster).
     */
    public function checkDepartmentModalHour(Request $request)
    {
        $employeeid = $request->get('employeeid');
        $hour = (int) $request->get('hour', 0);
        $minute = (int) $request->get('minute', 0);
        $day_hrs = (float) $request->get('day_hrs', 0);
        
        if (!$employeeid) {
            return Response::json(['error' => 'Employee ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        // Get department max day hours
        $employee = StoreEmployee::with('department')->find($employeeid);
        $maxDayHrs = $employee->department->day_max_time ?? 0;
        
        if ($day_hrs > 0 && $day_hrs > $maxDayHrs) {
            return Response::json(['status' => 'error']);
        }
        
        // Get department max week hours
        $maxWeekHrs = $employee->department->roster_max_time ?? 0;
        
        // Get all employees in same department (excluding current employee)
        $deptEmployees = StoreEmployee::where('departmentid', $employee->departmentid)
            ->where('storeid', $storeid)
            ->where('status', 'Active')
            ->where('employeeid', '!=', $employeeid)
            ->pluck('employeeid')
            ->toArray();
        
        // Calculate total hours for all employees in department
        $totaltime = 0;
        foreach ($deptEmployees as $empId) {
            $rosterDetails = Roster::where('employeeid', $empId)
                ->where('storeid', $storeid)
                ->where('work_status', 'on')
                ->get();
            
            foreach ($rosterDetails as $roster) {
                $start = strtotime($roster->start_time);
                $end = strtotime($roster->end_time);
                $diff = round(($end - $start) / 3600, 1);
                $totaltime += $diff;
            }
        }
        
        // Add current employee's hours
        $total = $totaltime + $hour;
        $emp_total = ($total * 60) + $minute;
        $dept_total = $maxWeekHrs * 60;
        
        if ($totaltime > $maxWeekHrs || $emp_total > $dept_total) {
            return Response::json(['status' => 'error']);
        }
        
        return Response::json(['status' => 'success']);
    }

    /**
     * Send order sheet to supplier via email.
     */
    public function sendOrderSheet(Request $request)
    {
        $purchaseOrderId = $request->get('purchase_orders_id');
        
        if (!$purchaseOrderId) {
            return Response::json(['status' => false, 'message' => 'Purchase Order ID required']);
        }
        
        $storeid = $this->getStoreId();
        
        try {
            // Get purchase order with supplier and store info
            $purchaseOrder = PurchaseOrder::with(['supplier', 'store'])
                ->where('purchase_orders_id', $purchaseOrderId)
                ->where('storeid', $storeid)
                ->first();
            
            if (!$purchaseOrder) {
                return Response::json(['status' => false, 'message' => 'Purchase Order not found']);
            }
            
            // Check if supplier has email
            if (!$purchaseOrder->supplier->supplier_email) {
                return Response::json(['status' => false, 'message' => 'Supplier email not configured']);
            }
            
            // Check if store email credentials are configured
            if (!$purchaseOrder->store->store_email || !$purchaseOrder->store->store_email_pass) {
                return Response::json(['status' => false, 'message' => 'Store email credentials not configured']);
            }
            
            // Get purchased products
            $purchasedProducts = DB::table('stoma_purchasedproducts as pp')
                ->select(
                    'pp.productid',
                    'pp.quantity',
                    'pp.product_price',
                    'pp.totalamount',
                    'sp.product_name',
                    'pm.purchasemeasure'
                )
                ->leftJoin('stoma_store_products as sp', 'pp.productid', '=', 'sp.productid')
                ->leftJoin('stoma_purchasemeasures as pm', 'pp.purchasemeasuresid', '=', 'pm.purchasemeasuresid')
                ->where('pp.purchase_orders_id', $purchaseOrderId)
                ->get();
            
            // Prepare email data
            $storeEmail = $purchaseOrder->store->store_email;
            $storeEmailPass = $purchaseOrder->store->store_email_pass;
            $supplierEmail = $purchaseOrder->supplier->supplier_email;
            $storeName = $purchaseOrder->store->storename ?? '';
            $supplierName = $purchaseOrder->supplier->supplier_name ?? '';
            $supplierAccNumber = $purchaseOrder->supplier->supplier_acc_number ?? '';
            $poId = $purchaseOrder->purchase_orders_id;
            $orderDate = $purchaseOrder->insertdate ? explode(' ', $purchaseOrder->insertdate)[0] : '';
            $deliveryDate = $purchaseOrder->delivery_date ?? '';
            $poNote = $purchaseOrder->po_note ?? '';
            
            // Create PHPMailer instance
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $storeEmail;
            $mail->Password = $storeEmailPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port = 465;
            
            // Recipients
            $mail->setFrom($storeEmail, '');
            $mail->addAddress($supplierEmail);
            $mail->addReplyTo($storeEmail, '');
            $mail->addBCC($storeEmail); // BCC store email
            
            // Subject
            $subject = 'Order From - ' . $storeName . ' - Acc No: ' . $supplierAccNumber;
            $mail->Subject = $subject;
            
            // Email body (matching CI format)
            $emailBody = '<html><body><table width="100%" border="1" style="border-collapse: collapse; border: 1px solid black;padding: 1%;margin: 1%;border-spacing:0px; width:100%">';
            
            $emailBody .= '<tr>';
            $emailBody .= '<td> From: ' . htmlspecialchars($storeName) . '</td>';
            $emailBody .= '<td>Acc-No: ' . htmlspecialchars($supplierAccNumber) . '</td>';
            $emailBody .= '<td>PO-No: ' . htmlspecialchars($poId) . '</td>';
            $emailBody .= '<td>Supplier: ' . htmlspecialchars($supplierName) . '</td>';
            $emailBody .= '</tr>';
            
            $emailBody .= '<tr>';
            $emailBody .= '<td>Order Date: </td>';
            $emailBody .= '<td>' . htmlspecialchars($orderDate) . '</td>';
            $emailBody .= '<td>Delivery Date: </td>';
            $emailBody .= '<td>' . htmlspecialchars($deliveryDate) . '</td>';
            $emailBody .= '</tr>';
            
            $emailBody .= '<tr>';
            $emailBody .= '<th colspan="4">Items</th>';
            $emailBody .= '</tr>';
            
            $emailBody .= '<tr>';
            $emailBody .= '<th>Item</th>';
            $emailBody .= '<th colspan="3">Qty.</th>';
            $emailBody .= '</tr>';
            
            foreach ($purchasedProducts as $product) {
                $emailBody .= '<tr>';
                $emailBody .= '<td>' . htmlspecialchars($product->product_name) . ' (' . htmlspecialchars($product->purchasemeasure ?? '') . ')</td>';
                $emailBody .= '<td colspan="3" style="text-align:center;">' . htmlspecialchars($product->quantity) . '</td>';
                $emailBody .= '</tr>';
            }
            
            $emailBody .= '<tr>';
            $emailBody .= '<th colspan="4">Notes</th>';
            $emailBody .= '</tr>';
            
            $emailBody .= '<tr>';
            $emailBody .= '<td colspan="4">' . htmlspecialchars($poNote) . '</td>';
            $emailBody .= '</tr>';
            
            $emailBody .= '</table></body></html>';
            
            // Content
            $mail->isHTML(true);
            $mail->Body = $emailBody;
            
            // Send email
            $status = $mail->send();
            
            return Response::json(['status' => $status]);
            
        } catch (Exception $e) {
            \Log::error('Failed to send order sheet email: ' . $e->getMessage());
            return Response::json(['status' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order sheet email: ' . $e->getMessage());
            return Response::json(['status' => false, 'message' => 'Failed to send email']);
        }
    }
}

