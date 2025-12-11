<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WeekRoster;
use App\Models\Week;
use App\Models\Year;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RosterController extends Controller
{
    /**
     * Display list of employee rosters.
     * Matches CI's my_profile/roster_list functionality.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $employee->storeid;
        $employeeid = $employee->employeeid;

        // Get last 5 rosters grouped by week (matching CI's get_my_roster)
        // CI uses: $this->db->group_by('weekid'); which returns one row per unique weekid
        // Since week_roster has multiple rows per weekid (one per day), we need one row per weekid
        // Step 1: Get the first wrid for each weekid (MIN wrid per weekid)
        $firstRosterPerWeek = DB::table('stoma_week_roster')
            ->select('weekid', DB::raw('MIN(wrid) as min_wrid'))
            ->where('storeid', $storeid)
            ->where('employeeid', $employeeid)
            ->groupBy('weekid')
            ->orderBy('weekid', 'desc')
            ->limit(5)
            ->get();
        
        if ($firstRosterPerWeek->isEmpty()) {
            $rosters = collect([]);
        } else {
            // Step 2: Get full details for these specific wrids
            $wrids = $firstRosterPerWeek->pluck('min_wrid')->toArray();
            
            $rosters = DB::table('stoma_week_roster as r')
                ->select(
                    'e.firstname',
                    'e.lastname',
                    'w.weeknumber',
                    'w.yearid',
                    'y.yearid as y_yearid',
                    'y.year',
                    'r.wrid',
                    'r.storeid',
                    'r.employeeid',
                    'r.departmentid',
                    'r.start_time',
                    'r.end_time',
                    'r.day',
                    'r.shift',
                    'r.day_date',
                    'r.work_status',
                    'r.weekid',
                    'r.status',
                    'r.insertdatetime',
                    'r.insertip',
                    'r.editdatetime',
                    'r.break_every_hrs',
                    'r.break_min',
                    'r.paid_break'
                )
                ->join('stoma_employee as e', 'e.employeeid', '=', 'r.employeeid')
                ->leftJoin('stoma_week as w', 'w.weekid', '=', 'r.weekid')
                ->leftJoin('stoma_year as y', 'y.yearid', '=', 'w.yearid')
                ->whereIn('r.wrid', $wrids)
                ->orderBy('r.weekid', 'desc')
                ->get();
        }

        return view('employee.roster.index', compact('rosters'));
    }

    /**
     * Display detailed view of a specific week roster.
     * Matches CI's my_profile/view_roster functionality.
     */
    public function show(string $storeid, string $employeeid, string $weekid): View
    {
        $storeid = base64_decode($storeid);
        $employeeid = base64_decode($employeeid);
        $weekid = base64_decode($weekid);

        // Get roster for the specific week
        $rosters = DB::table('stoma_week_roster as r')
            ->select(
                'e.firstname',
                'e.lastname',
                'r.*',
                'w.weeknumber',
                'y.year',
                'y.yearid'
            )
            ->join('stoma_employee as e', 'e.employeeid', '=', 'r.employeeid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'r.weekid')
            ->leftJoin('stoma_year as y', 'y.yearid', '=', 'w.yearid')
            ->where('r.storeid', $storeid)
            ->where('r.employeeid', $employeeid)
            ->where('r.weekid', $weekid)
            ->orderByRaw("FIELD(r.day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->get();

        $week = $rosters->first()->weeknumber ?? null;
        $year = $rosters->first()->year ?? null;

        return view('employee.roster.show', compact('rosters', 'storeid', 'employeeid', 'weekid', 'week', 'year'));
    }

    /**
     * Navigate to previous/next week roster.
     * Matches CI's my_profile/view_roster_next_previous functionality.
     */
    public function navigate(Request $request): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();
        $storeid = $request->input('storeid');
        $employeeid = $request->input('employeeid');
        $weekid = $request->input('weekid');
        $weeknumber = (int) $request->input('week');
        $yearnumber = (int) $request->input('year');

        if ($request->has('week_last')) {
            // Previous week
            if ($weeknumber == 1) {
                $year = $yearnumber - 1;
                $week = 52;
            } else {
                $week = $weeknumber - 1;
                $year = $yearnumber;
            }
        } elseif ($request->has('week_next')) {
            // Next week
            if ($weeknumber == 52) {
                $year = $yearnumber + 1;
                $week = 1;
            } else {
                if ($weeknumber != (int) date('W')) {
                    $week = $weeknumber + 1;
                    $year = $yearnumber;
                } else {
                    $week = (int) date('W');
                    $year = (int) date('Y');
                }
            }
        } else {
            $week = (int) date('W');
            $year = (int) date('Y');
        }

        // Get yearid
        $yearModel = Year::where('year', $year)->first();
        if (!$yearModel) {
            return redirect()->route('employee.roster.index')
                ->with('error', 'Year not found.');
        }

        // Get weekid
        $weekModel = Week::where('weeknumber', $week)
            ->where('yearid', $yearModel->yearid)
            ->first();

        if (!$weekModel) {
            return redirect()->route('employee.roster.index')
                ->with('error', 'Week not found.');
        }

        return redirect()->route('employee.roster.show', [
            'storeid' => base64_encode($storeid),
            'employeeid' => base64_encode($employeeid),
            'weekid' => base64_encode($weekModel->weekid)
        ]);
    }
}

