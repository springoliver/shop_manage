<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StoreOwner\DashboardService;
use App\Services\StoreOwner\ModuleService;
use App\Models\Store;
use App\Models\DashboardSettings;
use App\Models\Department;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ModuleService $moduleService;

    public function __construct(DashboardService $dashboardService, ModuleService $moduleService)
    {
        $this->dashboardService = $dashboardService;
        $this->moduleService = $moduleService;
    }

    /**
     * Display the dashboard.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get installed modules
        $installedModules = $this->moduleService->getInstalledModules($storeid);
        $installedModuleNames = array_column($installedModules, 'module');

        // Employee Statistics
        $empNewCount = $this->dashboardService->getEmployeeCountsByType('new');
        $empCount = $this->dashboardService->getEmployeeCountsByType('all');
        $empActiveCount = $this->dashboardService->getEmployeeCountsByType('active');
        $empDeactiveCount = $this->dashboardService->getEmployeeCountsByType('de-active');
        $empClosedCount = $this->dashboardService->getEmployeeCountsByType('closed');
        $clockInCount = $this->dashboardService->getClockedInEmployeesCount();

        // Employee Reviews
        $employeeReviews = $this->dashboardService->getEmployeeReviewsCount();
        $employeeReviewsDueCount = $this->dashboardService->getDueEmployeeReviewsCount();

        // Holiday Requests
        $holidayRequestCount = $this->dashboardService->getHolidayRequestsCountByType('all');
        $holidayRequestPendingCount = $this->dashboardService->getHolidayRequestsCountByType('pending');

        // Resignations
        $resignationCount = $this->dashboardService->getResignationCountByType('all');
        $resignationPendingCount = $this->dashboardService->getResignationCountByType('pending');

        // Delivery Dockets
        $deliveryDocketsCount = $this->dashboardService->getAwaitingDeliveryDocketsCount();

        // Daily Sales
        $todayAmount = $this->dashboardService->getSalesAmountByDate(date('Y-m-d'));
        $yesterdayAmount = $this->dashboardService->getSalesAmountByDate(date('Y-m-d', strtotime('-1 days')));
        $dailySalesAmount = $todayAmount ?: 0.00;
        $dailySalesPercentage = $this->dashboardService->getAmountPercentage($todayAmount, $yesterdayAmount);
        $dailyRefund = $this->dashboardService->getDailyRefundByDate(date('Y-m-d'));
        $dailyGift = $this->dashboardService->getDailyGiftByDate(date('Y-m-d'));
        $dailyCash = $this->dashboardService->getDailyCashByDate(date('Y-m-d'));

        // Weekly Sales
        $curWeek = date('W');
        $curYear = date('Y');
        if ($curWeek == 1) {
            $lastWeek = 52;
            $lastWeekYear = $curYear - 1;
        } else {
            $lastWeek = $curWeek - 1;
            $lastWeekYear = $curYear;
        }

        $result = $this->getStartAndEndDate($curWeek, $curYear);
        $saleWeekStartDate = $result['week_start'];
        $saleWeekEndDate = $result['week_end'];

        $resultLastWeek = $this->getStartAndEndDate($lastWeek, $lastWeekYear);
        $saleLastWeekStartDate = $resultLastWeek['week_start'];
        $saleLastWeekEndDate = $resultLastWeek['week_end'];

        $currentWeekAmount = $this->dashboardService->getSalesAmountByDates($saleWeekStartDate, $saleWeekEndDate);
        $prevWeekAmount = $this->dashboardService->getSalesAmountByDates($saleLastWeekStartDate, $saleLastWeekEndDate);
        $weeklysalesAmount = $currentWeekAmount ?: 0.00;
        $weeklysalesPercentage = $this->dashboardService->getAmountPercentage($currentWeekAmount, $prevWeekAmount);

        // Monthly Sales
        $dailyReport = $this->getMonthlyReports();
        
        // Yearly Sales
        $dailyYearlyReport = $this->getYearlyReports();

        // Get stores
        $stores = Store::where('storeownerid', $user->ownerid)->get();
        if ($stores->count() < 1) {
            Auth::guard('storeowner')->logout();
            return redirect()->route('storeowner.login')
                ->with('error', 'Your store does not verified. Please contact to admin.');
        }

        // Week Navigation Logic
        $week = $request->input('week', date('W'));
        $year = $request->input('year', date('Y'));

        if ($request->has('week_last')) {
            $weeknumber = $request->input('week');
            $yearnumber = $request->input('year');
            
            $totalWeek = 0;
            for ($i = 1; $i <= 12; $i++) {
                $totalWeek += $this->weeksInMonth($yearnumber, $i, 1);
            }

            if ($weeknumber == 1) {
                $year = $yearnumber - 1;
                $week = $totalWeek;
            } else {
                $week = $weeknumber - 1;
                $year = $yearnumber;
            }
        } elseif ($request->has('week_next')) {
            $weeknumber = $request->input('week');
            $yearnumber = $request->input('year');
            
            $totalWeek = 0;
            for ($i = 1; $i <= 12; $i++) {
                $totalWeek += $this->weeksInMonth($yearnumber, $i, 1);
            }

            if ($weeknumber == $totalWeek) {
                $year = $yearnumber + 1;
                $week = 1;
            } else {
                if ($weeknumber != date('W')) {
                    $week = $weeknumber + 1;
                    $year = $yearnumber;
                } else {
                    $week = date('W');
                    $year = date('Y');
                }
            }
        } else {
            $week = date('W');
            $year = date('Y');
            
            $totalWeek = 0;
            for ($i = 1; $i <= 12; $i++) {
                $totalWeek += $this->weeksInMonth($year, $i, 1);
            }

            if ($week == $totalWeek) {
                $year = $year - 1;
            }
        }

        if ($week == '53') {
            $year = $year - 1;
        }

        // Get week start and end dates
        $result = $this->getStartAndEndDate($week, $year);
        $weekStartDate = $result['week_start'];
        $weekEndDate = $result['week_end'];
        $weekEndDateForDisplay = $result['week_end'];
        $startDate = $result['week_start'];
        $endDate = $result['week_end'];

        $curdate = strtotime(date("Y-m-d"));
        $mydate = strtotime($endDate);

        if ($curdate > $mydate) {
            $endDate = $endDate;
        } else {
            $endDate = date("Y-m-d");
        }

        $start = date('d', strtotime($weekStartDate));
        
        // Current week sales data (7 days)
        $currentWeekAmount1 = [];
        for ($i = 1; $i <= 7; $i++) {
            if ($i == 1) {
                $j = 0;
            } else {
                $j = $j + 1;
            }
            $currentWeekAmount1[] = [
                $this->dashboardService->getCurrentWeekSaleData(date("Y-m-d", strtotime('+' . $j . ' day', strtotime($weekStartDate))))
            ];
        }

        $totalSales = 0;
        for ($j = 0; $j < 7; $j++) {
            if (empty($currentWeekAmount1[$j][0])) {
                $currentWeekAmount1[$j][0] = 0;
            }
            $totalSales = $totalSales + $currentWeekAmount1[$j][0];
        }

        // Department hours
        $departments = $this->dashboardService->getDepartmentHour2();
        
        $currentWeekHour = [];
        foreach ($departments as $depID => $depVL) {
            $totalWeekHour = 0;
            $currentWeekHour[$depID] = [];
            for ($i = 1; $i <= 7; $i++) {
                if ($i == 1) {
                    $j = 0;
                } else {
                    $j = $j + 1;
                }
                $hourData = $this->dashboardService->getCurrentWeekHourData2(
                    date("Y-m-d", strtotime('+' . $j . ' day', strtotime($weekStartDate))),
                    $week,
                    $depID
                );
                $currentWeekHour[$depID][$j] = $hourData;
                $totalWeekHour += $hourData['tHrsFloat'];
            }
            $currentWeekHour[$depID][7] = ['tHrsFloat' => $totalWeekHour];
        }

        // Last week data
        $lastWeekData = $this->getStartAndEndDate(($week - 1), $year);
        $lastWeekStartDate = $lastWeekData['week_start'];
        $lastWeekEndDate = $lastWeekData['week_end'];

        $lastWeekDataArr = [];
        for ($i = 1; $i <= 7; $i++) {
            if ($i == 1) {
                $j = 0;
            } else {
                $j = $j + 1;
            }
            $lastWeekDataArr[] = [
                $this->dashboardService->getLastWeekSaleData(date("Y-m-d", strtotime('+' . $j . ' day', strtotime($lastWeekStartDate))))
            ];
        }

        $totalLastWeekSalesData = 0;
        for ($j = 0; $j < 7; $j++) {
            if (empty($lastWeekDataArr[$j][0])) {
                $lastWeekDataArr[$j][0] = 0;
            }
            $totalLastWeekSalesData = $totalLastWeekSalesData + $lastWeekDataArr[$j][0];
        }

        $percentageOfLastWeekData = $this->dashboardService->getAmountPercentage($totalSales, $totalLastWeekSalesData);

        // Last week average data
        $lastWeekAvgData = [];
        for ($i = 0; $i < 7; $i++) {
            $lastWeekAvgData[] = $this->dashboardService->getAmountPercentage(
                $currentWeekAmount1[$i][0],
                $lastWeekDataArr[$i][0]
            );
        }

        // Average of hour (Sale per labour hour)
        $avgOfHour = [];
        if (!empty($departments)) {
            foreach ($departments as $depID => $depVL) {
                $currentWeekAmount1Total = 0;
                $currentWeekHourTotal = 0;
                for ($i = 0; $i < 7; $i++) {
                    if (!empty($currentWeekHour[$depID][$i]['tHrsFloat'])) {
                        $avgOfHour[$depID][$i] = (int)(($currentWeekAmount1[$i][0]) / ($currentWeekHour[$depID][$i]['tHrsFloat']));
                        $currentWeekAmount1Total += $currentWeekAmount1[$i][0];
                        $currentWeekHourTotal += $currentWeekHour[$depID][$i]['tHrsFloat'];
                    } else {
                        $avgOfHour[$depID][$i] = 0;
                    }
                }
                if (!empty($currentWeekHourTotal)) {
                    $avgOfHour[$depID][7] = (int)($currentWeekAmount1Total / $currentWeekHourTotal);
                } else {
                    $avgOfHour[$depID][7] = 0;
                }
            }
        }

        // Last year data
        $lastYearWeek = $this->getStartAndEndDate(($week - 1), $year);
        $lastYearWeekStartDate = date('Y-m-d', strtotime('-1 year', strtotime($lastYearWeek['week_start'])));
        $lastYearWeekEndDate = date('Y-m-d', strtotime('-1 year', strtotime($lastYearWeek['week_end'])));

        $lastYearData = [];
        for ($i = 1; $i <= 7; $i++) {
            if ($i == 1) {
                $j = 0;
            } else {
                $j = $j + 1;
            }
            $lastYearData[] = [
                $this->dashboardService->getLastYearSaleData(
                    date("Y-m-d", strtotime('+' . $j . ' day', strtotime($lastYearWeekStartDate))),
                    $lastYearWeekEndDate
                )
            ];
        }

        $totalYearSalesData = 0;
        for ($j = 0; $j < 7; $j++) {
            if (empty($lastYearData[$j][0])) {
                $lastYearData[$j][0] = 0;
            }
            $totalYearSalesData = $totalYearSalesData + $lastYearData[$j][0];
        }

        $percentageOfTotalYearData = $this->dashboardService->getAmountPercentage($totalSales, $totalYearSalesData);

        // Last year average data
        $lastYearAvgData = [];
        for ($i = 0; $i < 7; $i++) {
            $lastYearAvgData[] = $this->dashboardService->getAmountPercentage(
                $currentWeekAmount1[$i][0],
                $lastYearData[$i][0]
            );
        }

        // Target data
        $targetData = null;
        if (Schema::hasTable('stoma_sales_target')) {
            $targetData = DB::table('stoma_sales_target')
                ->where('storeid', $storeid)
                ->where('title', 'week')
                ->first();
        }

        $targetWeekData = [];
        if ($targetData) {
            $currentWeekTotalAmt = 0;
            $currentWeekTotalTarget = 0;
            for ($i = 0; $i < 7; $i++) {
                $targetWeekData[] = $this->dashboardService->getAmountPercentage(
                    $currentWeekAmount1[$i][0],
                    (float)$targetData->value
                );
                $currentWeekTotalAmt += $currentWeekAmount1[$i][0];
                $currentWeekTotalTarget += (float)$targetData->value;
            }
            $targetWeekData[] = $this->dashboardService->getAmountPercentage(
                $currentWeekTotalAmt,
                $currentWeekTotalTarget
            );
        } else {
            $targetWeekData = [];
        }

        // Compare to target (hours)
        $compareToTarget = [];
        if (!empty($departments)) {
            foreach ($departments as $depID => $depVL) {
                $compareToTarget[$depID][7] = 0;
                for ($i = 0; $i < 7; $i++) {
                    if (!empty($currentWeekHour[$depID][$i]['tHrsFloat'])) {
                        $targetHours = $depVL['total_hour'][$i] ?? 0;
                        $compareToTarget[$depID][$i] = (int)($currentWeekHour[$depID][$i]['tHrsFloat']) - (int)$targetHours;
                    } else {
                        $compareToTarget[$depID][$i] = 0;
                    }
                    $compareToTarget[$depID][7] += $compareToTarget[$depID][$i];
                }
            }
        }

        return view('storeowner.dashboard', compact(
            'installedModules',
            'installedModuleNames',
            'empNewCount',
            'empCount',
            'empActiveCount',
            'empDeactiveCount',
            'empClosedCount',
            'clockInCount',
            'employeeReviews',
            'employeeReviewsDueCount',
            'holidayRequestCount',
            'holidayRequestPendingCount',
            'resignationCount',
            'resignationPendingCount',
            'deliveryDocketsCount',
            'dailySalesAmount',
            'dailySalesPercentage',
            'dailyRefund',
            'dailyGift',
            'dailyCash',
            'weeklysalesAmount',
            'weeklysalesPercentage',
            'dailyReport',
            'dailyYearlyReport',
            'stores',
            'week',
            'year',
            'startDate',
            'endDate',
            'weekEndDateForDisplay',
            'start',
            'currentWeekAmount1',
            'totalSales',
            'lastWeekAvgData',
            'lastYearAvgData',
            'targetWeekData',
            'currentWeekHour',
            'compareToTarget',
            'avgOfHour',
            'departments',
            'percentageOfLastWeekData',
            'percentageOfTotalYearData'
        ));
    }

    /**
     * Get monthly reports
     */
    protected function getMonthlyReports(): array
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);

        $result = DB::table('stoma_daily_report')
            ->select(DB::raw('SUM(total_sell) as total_sell'), DB::raw('SUM(s_safe) as s_safe'))
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->where('storeid', $storeid)
            ->first();

        if ($result) {
            return [
                'total_sell' => (float)($result->total_sell ?? 0),
                's_safe' => (float)($result->s_safe ?? 0),
            ];
        }

        return ['total_sell' => 0, 's_safe' => 0];
    }

    /**
     * Get yearly reports
     */
    protected function getYearlyReports(): array
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);

        $result = DB::table('stoma_daily_report')
            ->select(DB::raw('SUM(total_sell) as total_yearly_sell'), DB::raw('SUM(s_safe) as s_yearly_safe'))
            ->whereYear('date', date('Y'))
            ->where('storeid', $storeid)
            ->first();

        if ($result) {
            return [
                'total_yearly_sell' => (float)($result->total_yearly_sell ?? 0),
                's_yearly_safe' => (float)($result->s_yearly_safe ?? 0),
            ];
        }

        return ['total_yearly_sell' => 0, 's_yearly_safe' => 0];
    }

    /**
     * Get start and end date for a week
     */
    protected function getStartAndEndDate(int $week, int $year): array
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $weekStart = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $weekEnd = $dto->format('Y-m-d');
        
        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ];
    }

    /**
     * Calculate weeks in a month
     */
    protected function weeksInMonth(int $year, int $month, int $startDayOfWeek): int
    {
        $numOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));
        $numOfWeeks = 0;
        
        for ($i = 1; $i <= $numOfDays; $i++) {
            $dayOfWeek = date('w', mktime(0, 0, 0, $month, $i, $year));
            if ($dayOfWeek == $startDayOfWeek) {
                $numOfWeeks++;
            }
        }
        
        return $numOfWeeks;
    }

    /**
     * Update dashboard settings (AJAX)
     */
    public function settings(Request $request)
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);

        $response = ['status' => 0, 'data' => []];

        if ($request->has('value')) {
            $existing = DashboardSettings::where('storeid', $storeid)->first();
            
            if ($existing) {
                $existing->sale_per_labour_hour = $request->input('value');
                $existing->save();
            } else {
                DashboardSettings::create([
                    'storeid' => $storeid,
                    'sale_per_labour_hour' => $request->input('value'),
                ]);
            }

            $response['status'] = 1;
            return response()->json($response);
        }

        return response()->json($response);
    }

    /**
     * Get dashboard settings (AJAX)
     */
    public function getSettings()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);

        $response = ['status' => 0, 'data' => []];
        
        $settings = DashboardSettings::where('storeid', $storeid)->first();
        
        if ($settings) {
            $response['status'] = 1;
            $response['data'] = [$settings->toArray()];
        }

        return response()->json($response);
    }
}
