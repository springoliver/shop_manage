<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the employee dashboard.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $employee->storeid;

        // Get statistics data
        $employeereviewsDueCount = $this->getDueEmployeeReviewsCount($storeid);
        $holidayrequestCount = $this->getHolidayRequestCountByType($storeid, 'all');
        $holidayrequestPendingCount = $this->getHolidayRequestCountByType($storeid, 'pending');
        $resignationCount = $this->getResignationCountByType($storeid, 'all');
        $resignationPendingCount = $this->getResignationCountByType($storeid, 'pending');
        $clockInCount = $this->getClockedInEmployeesCount($storeid);
        $deliveryDocketsCount = $this->getAwaitingDeliveryDocketsCount($storeid);

        return view('employee.dashboard', compact(
            'storeid',
            'employeereviewsDueCount',
            'holidayrequestCount',
            'holidayrequestPendingCount',
            'resignationCount',
            'resignationPendingCount',
            'clockInCount',
            'deliveryDocketsCount'
        ));
    }

    /**
     * Get due employee reviews count.
     */
    private function getDueEmployeeReviewsCount(int $storeid): int
    {
        return DB::table('stoma_employee_reviews_new')
            ->join('stoma_employee', 'stoma_employee_reviews_new.employeeid', '=', 'stoma_employee.employeeid')
            ->where('stoma_employee.storeid', $storeid)
            ->whereDate('next_review_date', '<=', now())
            ->count();
    }

    /**
     * Get holiday request count by type.
     */
    private function getHolidayRequestCountByType(int $storeid, string $type): int
    {
        $query = DB::table('stoma_holiday_request')
            ->join('stoma_employee', 'stoma_holiday_request.employeeid', '=', 'stoma_employee.employeeid')
            ->where('stoma_employee.storeid', $storeid);

        if ($type === 'pending') {
            $query->where('stoma_holiday_request.status', 'Pending');
        }

        return $query->count();
    }

    /**
     * Get resignation count by type.
     */
    private function getResignationCountByType(int $storeid, string $type): int
    {
        $query = DB::table('stoma_resignation')
            ->join('stoma_employee', 'stoma_resignation.employeeid', '=', 'stoma_employee.employeeid')
            ->where('stoma_employee.storeid', $storeid);

        if ($type === 'pending') {
            $query->where('stoma_resignation.status', 'Pending');
        }

        return $query->count();
    }

    /**
     * Get clocked in employees count.
     */
    private function getClockedInEmployeesCount(int $storeid): int
    {
        // CI checks for status='clockout' - matching CI's implementation
        return DB::table('stoma_emp_login_time')
            ->where('storeid', $storeid)
            ->where('status', 'clockout')
            ->count();
    }

    /**
     * Get awaiting delivery dockets count.
     */
    private function getAwaitingDeliveryDocketsCount(int $storeid): int
    {
        // CI checks for deliverydocketstatus='No' - matching CI's implementation
        return DB::table('stoma_purchase_orders')
            ->where('storeid', $storeid)
            ->where('deliverydocketstatus', 'No')
            ->count();
    }

    /**
     * Get sales chart weekly data (AJAX endpoint).
     */
    public function getSalesChartWeekly()
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $employee->storeid;

        $data = DB::table('stoma_daily_report')
            ->select(
                DB::raw('WEEK(date - INTERVAL 1 DAY) as week'),
                DB::raw('YEAR(date) as year'),
                DB::raw('SUM(total_sell) as total_sell')
            )
            ->where('storeid', $storeid)
            ->groupBy('week', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('week', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'week' => (string)$item->week,
                    'year' => (string)$item->year,
                    'total_sell' => (float)$item->total_sell
                ];
            });

        return response()->json($data);
    }

    /**
     * Get employee hours chart weekly data (AJAX endpoint).
     */
    public function getHoursChartWeekly()
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $employee->storeid;

        // Since hours_worked is VARCHAR in CI, we need to cast it for SUM
        $data = DB::table('stoma_emp_payroll_hrs')
            ->select(
                'weekno',
                'year',
                DB::raw('SUM(CAST(hours_worked AS DECIMAL(10,2))) as hours_worked')
            )
            ->where('storeid', $storeid)
            ->groupBy('year', 'weekno')
            ->orderBy('year', 'ASC')
            ->orderBy('weekno', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'weekno' => (string)$item->weekno,
                    'year' => (string)$item->year,
                    'hours_worked' => (float)($item->hours_worked ?? 0)
                ];
            });

        return response()->json($data);
    }

    /**
     * Get purchase orders chart weekly data (AJAX endpoint).
     */
    public function getPoChartWeekly()
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $employee->storeid;

        $data = DB::table('stoma_purchase_orders')
            ->select(
                DB::raw('WEEK(delivery_date) as week'),
                DB::raw('YEAR(delivery_date) as year'),
                DB::raw('SUM(amount_inc_tax) as total_amount')
            )
            ->where('storeid', $storeid)
            ->where('purchase_orders_type', 'Purchase order')
            ->groupBy('week', 'year')
            ->orderBy('year', 'ASC')
            ->orderBy('week', 'ASC')
            ->get()
            ->map(function($item) {
                return [
                    'week' => (string)$item->week,
                    'year' => (string)$item->year,
                    'total_amount' => (float)$item->total_amount
                ];
            });

        return response()->json($data);
    }
}

