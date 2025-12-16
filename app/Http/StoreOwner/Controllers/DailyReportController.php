<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Get weekly sales chart data for dashboard (AJAX).
     * Matches CI's dailyreport/get_sales_chart_weekly.
     */
    public function getSalesChartWeekly()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid || $storeid == 0) {
            return response()->json([]);
        }
        
        // Match Employee DashboardController implementation exactly (which is working)
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
}

