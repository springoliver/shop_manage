<?php

namespace App\Services\StoreOwner;

use App\Models\StoreEmployee as Employee;
use App\Models\EmployeeReview;
use App\Models\HolidayRequest;
use App\Models\Resignation;
use App\Models\Department;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    protected int $storeid;

    public function __construct()
    {
        $user = Auth::guard('storeowner')->user();
        $this->storeid = session('storeid', $user->stores->first()->storeid ?? 0);
    }

    /**
     * Get employee counts by type
     */
    public function getEmployeeCountsByType(string $type): int
    {
        $query = Employee::where('storeid', $this->storeid)
            ->where('status', '!=', 'Closed');

        switch ($type) {
            case 'active':
                $query->where('status', 'Active');
                break;
            case 'de-active':
                $query->where('status', 'Deactivate');
                break;
            case 'new':
                $query->where('signupdate', '>=', now()->subDays(7));
                break;
            case 'closed':
                $query->where('status', 'Closed');
                break;
            case 'all':
            default:
                break;
        }

        return $query->count();
    }

    /**
     * Get clocked-in employees count
     */
    public function getClockedInEmployeesCount(): int
    {
        return DB::table('stoma_emp_login_time')
            ->where('storeid', $this->storeid)
            ->where('status', 'clockout')
            ->count();
    }

    /**
     * Get all employee reviews count
     */
    public function getEmployeeReviewsCount(): int
    {
        return EmployeeReview::where('storeid', $this->storeid)->count();
    }

    /**
     * Get due employee reviews count
     */
    public function getDueEmployeeReviewsCount(): int
    {
        return EmployeeReview::join('stoma_employee', 'stoma_employee_reviews_new.employeeid', '=', 'stoma_employee.employeeid')
            ->where('stoma_employee_reviews_new.storeid', $this->storeid)
            ->whereRaw('stoma_employee_reviews_new.next_review_date <= DATE_ADD(NOW(), INTERVAL 15 DAY)')
            ->where('stoma_employee.status', 'Active')
            ->count();
    }

    /**
     * Get holiday requests count by type
     */
    public function getHolidayRequestsCountByType(string $type): int
    {
        $query = HolidayRequest::where('storeid', $this->storeid)
            ->where('from_date', '>', now());

        if ($type === 'pending') {
            $query->where('status', 'Pending');
        }

        return $query->count();
    }

    /**
     * Get resignation count by type
     */
    public function getResignationCountByType(string $type): int
    {
        $query = Resignation::where('storeid', $this->storeid);

        if ($type === 'pending') {
            $query->where('status', 'Pending');
        }

        return $query->count();
    }

    /**
     * Get awaiting delivery dockets count
     */
    public function getAwaitingDeliveryDocketsCount(): int
    {
        return DB::table('stoma_purchase_orders')
            ->where('storeid', $this->storeid)
            ->where('deliverydocketstatus', 'No')
            ->count();
    }

    /**
     * Get sales amount by date
     */
    public function getSalesAmountByDate(string $date): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereDate('date', $date)
            ->value(DB::raw('SUM(total_sell)'));

        return (float)($result ?? 0);
    }

    /**
     * Get sales amount by date range
     */
    public function getSalesAmountByDates(string $startDate, string $endDate): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereBetween('date', [$endDate, $startDate])
            ->value(DB::raw('SUM(total_sell)'));

        return (float)($result ?? 0);
    }

    /**
     * Get daily refund by date
     */
    public function getDailyRefundByDate(string $date): float
    {
        // Check if table exists, return 0 if not
        if (!Schema::hasTable('stoma_refund')) {
            return 0.0;
        }
        
        $result = DB::table('stoma_refund')
            ->where('storeid', $this->storeid)
            ->whereDate('insertdate', $date)
            ->value(DB::raw('SUM(amount)'));

        return (float)($result ?? 0);
    }

    /**
     * Get daily gift by date
     */
    public function getDailyGiftByDate(string $date): float
    {
        // Check if table exists, return 0 if not
        if (!Schema::hasTable('stoma_giftvoucher')) {
            return 0.0;
        }
        
        // Gift voucher table may not have a date field - return 0 for now
        // This can be updated when the gift voucher table structure is confirmed
        return 0.0;
    }

    /**
     * Get daily cash by date
     */
    public function getDailyCashByDate(string $date): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereDate('date', $date)
            ->value(DB::raw('SUM(today_cash)'));

        return (float)($result ?? 0);
    }

    /**
     * Calculate percentage difference between two amounts
     */
    public function getAmountPercentage(float $current, float $prev): array
    {
        $per = 0.0;
        $status = '';

        if ($current < $prev) {
            $status = 'loss';
            $diff = $prev - $current;
            if ($current == 0) {
                $current = 1;
            }
            $temp = ($diff / $prev) * 100;
            $per = round($temp, 2);
            if ($per > 101) {
                $per = 100;
            }
        } elseif ($prev < $current) {
            $status = 'profit';
            $diff = $current - $prev;
            if ($prev == 0) {
                $prev = 1;
            }
            $per = round((($diff / $prev) * 100), 2);
            if ($per > 101) {
                $per = 100;
            }
        }

        return ['percentage' => $per . '%', 'status' => $status];
    }

    /**
     * Get current week sale data for a specific date
     */
    public function getCurrentWeekSaleData(string $date): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereDate('date', $date)
            ->value(DB::raw('SUM(total_sell)'));

        return (float)($result ?? 0);
    }

    /**
     * Get current week hour data for a specific date and department
     */
    public function getCurrentWeekHourData2(string $date, int $week, int $departmentId): array
    {
        $results = DB::table('stoma_emp_login_time as d')
            ->join('stoma_employee as e', 'd.employeeid', '=', 'e.employeeid')
            ->leftJoin('stoma_week as w', 'd.weekid', '=', 'w.weekid')
            ->where('d.storeid', $this->storeid)
            ->where('e.departmentid', $departmentId)
            ->where('w.weeknumber', $week)
            ->whereDate('d.clockin', $date)
            ->select('d.clockin', 'd.clockout', 'd.employeeid', 'e.departmentid')
            ->get();

        $totalMinutes = 0;
        
        foreach ($results as $row) {
            if ($row->clockin && $row->clockout) {
                $clockin = strtotime($row->clockin);
                $clockout = strtotime($row->clockout);
                $secondsDiff = $clockout - $clockin;
                $minutes = ($secondsDiff / 60);
                $totalMinutes += $minutes;
            }
        }

        $hours = floor($totalMinutes / 60);
        $mins = $totalMinutes % 60;
        $tHrsFloat = (float)($hours + ($mins / 60));

        return ['tHrsFloat' => $tHrsFloat];
    }

    /**
     * Get department hours with settings
     */
    public function getDepartmentHour2(): array
    {
        $departments = Department::where('storeid', $this->storeid)->get();
        $settings = $this->getDashboardSettings();
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $result = [];
        
        foreach ($departments as $dept) {
            $deptArray = $dept->toArray();
            $result[$dept->departmentid] = $deptArray;
            $result[$dept->departmentid]['total_hour'] = [];
            
            if (!empty($settings['sale_per_labour_hour']) && $settings['sale_per_labour_hour'] == 2) {
                // Day basis - use individual day fields
                for ($i = 0; $i < 7; $i++) {
                    $dayField = $days[$i];
                    $result[$dept->departmentid]['total_hour'][] = $dept->$dayField ?? ($dept->target_hours ?? 0);
                }
            } else {
                // Flat - use target_hours for all days
                for ($i = 0; $i < 7; $i++) {
                    $result[$dept->departmentid]['total_hour'][] = $dept->target_hours ?? 0;
                }
            }
        }
        
        return $result;
    }

    /**
     * Get dashboard settings
     */
    public function getDashboardSettings(): array
    {
        $settings = DB::table('stoma_dashboard_settings')
            ->where('storeid', $this->storeid)
            ->first();

        if (!$settings) {
            // Create default settings
            DB::table('stoma_dashboard_settings')->insert([
                'storeid' => $this->storeid,
                'sale_per_labour_hour' => 1,
            ]);
            return ['sale_per_labour_hour' => 1];
        }

        return [
            'sale_per_labour_hour' => $settings->sale_per_labour_hour ?? 1,
        ];
    }

    /**
     * Get last week sale data for a specific date
     */
    public function getLastWeekSaleData(string $date): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereDate('date', $date)
            ->value(DB::raw('SUM(total_sell)'));

        return (float)($result ?? 0);
    }

    /**
     * Get last year sale data for a specific date
     */
    public function getLastYearSaleData(string $date, string $endDate): float
    {
        $result = DB::table('stoma_daily_report')
            ->where('storeid', $this->storeid)
            ->whereDate('date', $date)
            ->value(DB::raw('SUM(total_sell)'));

        return (float)($result ?? 0);
    }

    /**
     * Get stores by owner
     */
    public function getStoresByOwner(int $ownerId): array
    {
        return Store::where('ownerid', $ownerId)->get()->toArray();
    }
}

