<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * The dashboard service instance.
     *
     * @var DashboardService
     */
    protected DashboardService $dashboardService;

    /**
     * Create a new controller instance.
     *
     * @param DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $statistics = $this->dashboardService->getStatistics();
        $mindate = $this->dashboardService->getOwnerMinimumDate();
        $type = 'owner';
        $utype = '';
        $syear = null;
        $smonth = null;
        $chartData = $this->prepareChartData($utype, $syear, $smonth);

        return view('admin.dashboard', compact('statistics', 'mindate', 'type', 'utype', 'syear', 'smonth', 'chartData'));
    }

    /**
     * Display owner graph with different time periods.
     *
     * @param Request $request
     * @param string|null $utype
     * @return View
     */
    public function owner(Request $request, ?string $utype = ''): View
    {
        $statistics = $this->dashboardService->getStatistics();
        $mindate = $this->dashboardService->getOwnerMinimumDate();
        $type = 'owner';

        // Get year and month from request (works for both GET and POST)
        $syear = $request->input('syear');
        $smonth = $request->input('smonth');

        // Prepare chart data based on time period
        $chartData = $this->prepareChartData($utype, $syear, $smonth);

        return view('admin.dashboard', compact('statistics', 'mindate', 'type', 'utype', 'syear', 'smonth', 'chartData'));
    }

    /**
     * Prepare chart data based on time period.
     *
     * @param string|null $utype
     * @param string|null $syear
     * @param string|null $smonth
     * @return array
     */
    private function prepareChartData(?string $utype, ?string $syear, ?string $smonth): array
    {
        $data = [];

        if ($utype == '' || $utype == 'weekly') {
            // Weekly data
            $getdate = strtotime(date('Y-m-d'));
            $dow = date('w', $getdate);
            $offset = $dow - 1;
            if ($offset < 0) {
                $offset = 6;
            }
            $monday = $getdate - ($offset * 86400);
            $startdate = date('Y-m-d', $monday);
            
            for($j = 0; $j < 7; $j++) {
                $s_date = strtotime("+{$j} day", strtotime($startdate));
                $s_date = date('Y-m-d', $s_date);
                $count = $this->dashboardService->getOwnerDetails($s_date);
                $data[] = [
                    'label' => date('D', strtotime($s_date)) . ' - ' . date('d', strtotime($s_date)),
                    'value' => $count
                ];
            }
        } elseif ($utype == 'monthly') {
            // Monthly data
            $month = date('m');
            $year = date('Y');
            $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $startdate = "1-{$month}-{$year}";
            
            for($i = 0; $i < $num; $i++) {
                $s_date = strtotime("+{$i} day", strtotime($startdate));
                $s_date = date('Y-m-d', $s_date);
                $count = $this->dashboardService->getOwnerDetails($s_date);
                $data[] = [
                    'label' => date('d', strtotime($s_date)),
                    'value' => $count
                ];
            }
        } elseif ($utype == 'monthyear') {
            if ($syear != '' && $smonth != '') {
                $num = cal_days_in_month(CAL_GREGORIAN, $smonth, $syear);
                $startdate = "1-{$smonth}-{$syear}";
                
                for($i = 0; $i < $num; $i++) {
                    $s_date = strtotime("+{$i} day", strtotime($startdate));
                    $s_date = date('Y-m-d', $s_date);
                    $count = $this->dashboardService->getOwnerDetails($s_date);
                    $data[] = [
                        'label' => date('d', strtotime($s_date)),
                        'value' => $count
                    ];
                }
            } elseif ($syear != '' && $smonth == '') {
                // Yearly data by month
                for($i = 1; $i <= 12; $i++) {
                    $startdate = "1-{$i}-{$syear}";
                    $enddate = date('Y-m-t', strtotime($startdate));
                    $startdate = date('Y-m-d', strtotime($startdate));
                    $count = $this->dashboardService->getMonthlyOwner($startdate, $enddate);
                    $monthName = date('M', mktime(0, 0, 0, $i, 1));
                    $data[] = [
                        'label' => $monthName,
                        'value' => $count
                    ];
                }
            } elseif ($syear == '' && $smonth != '') {
                $year = date('Y');
                $num = cal_days_in_month(CAL_GREGORIAN, $smonth, $year);
                $startdate = "1-{$smonth}-{$year}";
                
                for($i = 0; $i < $num; $i++) {
                    $s_date = strtotime("+{$i} day", strtotime($startdate));
                    $s_date = date('Y-m-d', $s_date);
                    $count = $this->dashboardService->getOwnerDetails($s_date);
                    $data[] = [
                        'label' => date('d', strtotime($s_date)),
                        'value' => $count
                    ];
                }
            } else {
                // All years
                $mindate = $this->dashboardService->getOwnerMinimumDate();
                $minimumyear = $mindate ? date('Y', strtotime($mindate)) : date('Y');
                $currentyear = date('Y');
                $diff = $currentyear - $minimumyear;
                
                for($i = 0; $i <= $diff; $i++) {
                    $startdate = "1-1-" . ($minimumyear + $i);
                    $enddate = "31-12-" . ($minimumyear + $i);
                    $startdate = date('Y-m-d', strtotime($startdate));
                    $enddate = date('Y-m-d', strtotime($enddate));
                    $count = $this->dashboardService->getMonthlyOwner($startdate, $enddate);
                    $data[] = [
                        'label' => (string)($minimumyear + $i),
                        'value' => $count
                    ];
                }
            }
        } else {
            // Default to weekly
            $getdate = strtotime(date('Y-m-d'));
            $dow = date('w', $getdate);
            $offset = $dow - 1;
            if ($offset < 0) {
                $offset = 6;
            }
            $monday = $getdate - ($offset * 86400);
            $startdate = date('Y-m-d', $monday);
            
            for($j = 0; $j < 7; $j++) {
                $s_date = strtotime("+{$j} day", strtotime($startdate));
                $s_date = date('Y-m-d', $s_date);
                $count = $this->dashboardService->getOwnerDetails($s_date);
                $data[] = [
                    'label' => date('D', strtotime($s_date)) . ' - ' . date('d', strtotime($s_date)),
                    'value' => $count
                ];
            }
        }

        return $data;
    }
}

