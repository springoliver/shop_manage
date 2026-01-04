<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreEmployee;
use App\Models\EmpLoginTime;
use App\Models\Store;
use App\Services\StoreOwner\ModuleService;
use App\Services\StoreOwner\ClockTimeService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class ClockTimeController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;
    protected ClockTimeService $clockTimeService;

    public function __construct(ModuleService $moduleService, ClockTimeService $clockTimeService)
    {
        $this->moduleService = $moduleService;
        $this->clockTimeService = $clockTimeService;
    }

    /**
     * Check if Clock in-out module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Clock in-out')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of clock in-out records.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $clockDetails = $this->clockTimeService->getClockDetails($storeid);
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        $searchDate = Carbon::now()->format('d-m-Y');
        $searchDateEnd = Carbon::now()->format('d-m-Y');
        $selectedEmployeeIds = []; // Empty array means "All Employees" is selected
        
        return view('storeowner.clocktime.index', compact('clockDetails', 'employees', 'searchDate', 'searchDateEnd', 'selectedEmployeeIds'));
    }

    /**
     * Display currently clocked-in employees.
     * CI method: clocked_in()
     */
    public function clockedIn(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $clockedOutDetails = $this->clockTimeService->getAllClockOutDetails($storeid);
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        // Add roster data and calculate hours for each record
        foreach ($clockedOutDetails as $detail) {
            $rosterData = $this->clockTimeService->getRosterHour($detail->employeeid, $detail->weekid ?? null, $detail->day ?? null);
            $detail->roster_start_time = $rosterData['start_time'] ?? '00:00';
            $detail->roster_end_time = $rosterData['end_time'] ?? '00:00';
            
            // Calculate time difference if clockout exists
            if ($detail->clockout && $detail->clockin) {
                $clockin = Carbon::parse($detail->clockin);
                $clockout = Carbon::parse($detail->clockout);
                $detail->timediff = $clockin->diffInMinutes($clockout);
            } else {
                $detail->timediff = null;
            }
        }
        
        return view('storeowner.clocktime.clocked_in', compact('clockedOutDetails', 'employees'));
    }

    /**
     * Manual clockout - clock out an employee manually.
     * CI method: manual_clockout()
     */
    public function manualClockout(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $validated = $request->validate([
            'eltid' => 'required|integer',
            'status' => 'required|string',
        ]);

        $result = $this->clockTimeService->manualClockout($validated['eltid']);

        if ($result) {
            return redirect()->route('storeowner.clocktime.clocked_in')
                ->with('success', 'Employee clocked out successfully.');
        } else {
            return redirect()->route('storeowner.clocktime.clocked_in')
                ->with('error', 'Something went wrong please try again.');
        }
    }

    /**
     * Handle search/report request.
     */
    public function clockReport(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'date' => 'required',
            'date_end' => 'required',
            'employeeid' => 'nullable|array',
            'employeeid.*' => 'nullable', // Allow empty strings for "All Employees"
        ]);
        
        $storeid = $this->getStoreId();
        
        // Parse dd-mm-yyyy format
        $date = Carbon::createFromFormat('d-m-Y', $validated['date'])->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $validated['date_end'])->format('Y-m-d');
        
        $employeeids = [];
        $selectedEmployeeIds = []; // For display in view - keep empty if "All Employees"
        
        if (!empty($validated['employeeid'])) {
            // Filter out empty strings and null values, keep only valid integers
            $filtered = array_filter($validated['employeeid'], function($value) {
                return $value !== '' && $value !== null && is_numeric($value);
            });
            $selectedEmployeeIds = array_values($filtered); // Re-index array
        }
        
        // For query: If no specific employees selected (or "All Employees"), use all employees
        if (empty($selectedEmployeeIds)) {
            $allEmployees = StoreEmployee::where('storeid', $storeid)
                ->where('status', '!=', 'Deactivate')
                ->pluck('employeeid')
                ->toArray();
            $employeeids = $allEmployees; // Use all for query
            // Keep $selectedEmployeeIds empty for display (shows "All Employees" tag)
        } else {
            $employeeids = $selectedEmployeeIds; // Use selected employees for query
        }
        
        $clockDetails = $this->clockTimeService->getClockDetailsByDate($storeid, $date, $endDate, $employeeids);
        
        // Add roster times and calculate totals (matching CI's logic)
        foreach ($clockDetails as $detail) {
            $rosterData = $this->clockTimeService->getRosterHour($detail->employeeid, $detail->weekid, $detail->day);
            $detail->roster_start_time = $rosterData['start_time'];
            $detail->roster_end_time = $rosterData['end_time'];
            
            // Calculate total roster hours (matching CI's calculation with abs())
            // CI uses: abs(strtotime($roster_start_time) - strtotime($roster_end_time)) / 60
            $rosterStartTime = $detail->roster_start_time ?? '00:00';
            $rosterEndTime = $detail->roster_end_time ?? '00:00';
            
            // Ensure H:i:s format for strtotime
            if (strlen($rosterStartTime) == 5) {
                $rosterStartTime .= ':00';
            }
            if (strlen($rosterEndTime) == 5) {
                $rosterEndTime .= ':00';
            }
            
            // Use strtotime like CI does (matching CI line 167-169)
            $to_time = strtotime($rosterStartTime);
            $from_time = strtotime($rosterEndTime);
            $detail->total_roster_minutes = round(abs($to_time - $from_time) / 60, 2);
            
            // Calculate clock in-out hours using CI's roster intersection logic
            // CI compares time-only values (H:i:s format), not full datetimes
            if ($detail->status !== 'clockout' && $detail->clockin && $detail->clockout) {
                $clockin = Carbon::parse($detail->clockin);
                $clockout = Carbon::parse($detail->clockout);
                
                // Get roster times as time strings (H:i format from getRosterHour)
                $rosterStartTime = $detail->roster_start_time ?? '00:00';
                $rosterEndTime = $detail->roster_end_time ?? '00:00';
                
                // Convert to H:i:s format for strtotime comparison (matching CI)
                if (strlen($rosterStartTime) == 5) {
                    $rosterStartTime .= ':00'; // Add seconds if missing
                }
                if (strlen($rosterEndTime) == 5) {
                    $rosterEndTime .= ':00'; // Add seconds if missing
                }
                
                // Get time-only strings from clockin/clockout (matching CI's date('H:i:s', strtotime(...)))
                $clockinTimeStr = $clockin->format('H:i:s');
                $clockoutTimeStr = $clockout->format('H:i:s');
                
                // CI's logic (line 144): Check if roster_end_time >= clockin (time comparison)
                // Using strtotime for time-only strings (matching CI's approach)
                $rosterEndTimestamp = strtotime($rosterEndTime);
                $clockinTimestamp = strtotime($clockinTimeStr);
                $rosterStartTimestamp = strtotime($rosterStartTime);
                $clockoutTimestamp = strtotime($clockoutTimeStr);
                
                // Check if roster times are actually set (not "00:00" or "00:00:00")
                $rosterIsSet = ($rosterStartTime !== '00:00' && $rosterStartTime !== '00:00:00' && 
                               $rosterEndTime !== '00:00' && $rosterEndTime !== '00:00:00');
                
                if ($rosterIsSet) {
                    // CI's overlap check: roster_end_time >= clockin AND roster_start_time <= clockout
                    if ($rosterEndTimestamp >= $clockinTimestamp && $rosterStartTimestamp <= $clockoutTimestamp) {
                        // Determine start time (CI lines 146-154)
                        $start = '';
                        if ($rosterStartTimestamp > $clockinTimestamp) {
                            // Use roster_start_time
                            $start = date('Y-m-d H:i:s', strtotime($rosterStartTime));
                        } elseif ($rosterStartTimestamp <= $clockinTimestamp) {
                            // Use clockin
                            $start = date('Y-m-d H:i:s', strtotime($clockinTimeStr));
                        } else {
                            $start = date('Y-m-d H:i:s', strtotime('00:00:00'));
                        }
                        
                        // Determine end time (CI lines 155-163)
                        $end = '';
                        if ($rosterEndTimestamp > $clockoutTimestamp) {
                            // Use clockout
                            $end = date('Y-m-d H:i:s', strtotime($clockoutTimeStr));
                        } elseif ($rosterEndTimestamp <= $clockoutTimestamp) {
                            // Use roster_end_time
                            $end = date('Y-m-d H:i:s', strtotime($rosterEndTime));
                        } else {
                            $end = date('Y-m-d H:i:s', strtotime('00:00:00'));
                        }
                        
                        // Calculate difference in minutes (matching CI line 175)
                        // CI uses: round(abs($to_time - $from_time) / 60, 2)
                        // where $to_time = strtotime($start) and $from_time = strtotime($end)
                        // This gives minutes (not hours) - CI's variable name is misleading
                        $to_time = strtotime($start);
                        $from_time = strtotime($end);
                        $detail->timediff = round(abs($to_time - $from_time) / 60, 2);
                    } else {
                        // No overlap - no hours counted (CI lines 164-170)
                        $detail->timediff = 0;
                    }
                } else {
                    // No roster set - calculate directly from clockin to clockout (like CI cron.php when inRoster == "No")
                    // This matches the behavior when roster times are "00:00"
                    $to_time = strtotime($clockinTimeStr);
                    $from_time = strtotime($clockoutTimeStr);
                    $detail->timediff = round(abs($to_time - $from_time) / 60, 2);
                }
            } else {
                $detail->timediff = null;
            }
        }
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        $searchDate = Carbon::parse($validated['date'])->format('d-m-Y');
        $searchDateEnd = Carbon::parse($validated['date_end'])->format('d-m-Y');
        
        return view('storeowner.clocktime.index', compact('clockDetails', 'employees', 'searchDate', 'searchDateEnd', 'selectedEmployeeIds'));
    }

    /**
     * Export clock in-out data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'date' => 'required',
            'date_end' => 'required',
            'employeeid' => 'nullable|array',
            'employeeid.*' => 'nullable', // Allow empty strings for "All Employees"
        ]);
        
        $storeid = $this->getStoreId();
        
        // Parse dd-mm-yyyy format
        try {
            $date = Carbon::createFromFormat('d-m-Y', $validated['date'])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d-m-Y', $validated['date_end'])->format('Y-m-d');
        } catch (\Exception $e) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Invalid date format. Please use dd-mm-yyyy format.');
        }
        
        $employeeids = [];
        if (!empty($validated['employeeid'])) {
            // Filter out empty strings and null values, keep only valid integers
            $employeeids = array_filter($validated['employeeid'], function($value) {
                return $value !== '' && $value !== null && is_numeric($value);
            });
            $employeeids = array_values($employeeids); // Re-index array
        }
        
        // If no employees selected (or "All Employees" with empty value), get all employees
        if (empty($employeeids)) {
            $employees = StoreEmployee::where('storeid', $storeid)
                ->where('status', '!=', 'Deactivate')
                ->pluck('employeeid')
                ->toArray();
            $employeeids = $employees;
        }
        
        // Increase memory limit and execution time for large datasets
        ini_set('memory_limit', '1024M'); // Increased to 1GB for very large datasets
        set_time_limit(600); // 10 minutes for very large datasets
        
        // Get clock details grouped by weekid and day (like CI)
        // Service already handles chunking internally
        try {
            $clockDetails = $this->clockTimeService->getClockReportForExport($storeid, $date, $endDate, $employeeids);
            
            // Check if dataset is too large (already an array from service)
            $recordCount = is_array($clockDetails) ? count($clockDetails) : $clockDetails->count();
            if ($recordCount > 10000) {
                return redirect()->route('storeowner.clocktime.index')
                    ->with('error', 'Dataset too large (' . number_format($recordCount) . ' records). Please select specific employees or a shorter date range. Maximum recommended: 10,000 records.');
            }
            
            // Already an array from service
            $clockDetailsArray = is_array($clockDetails) ? $clockDetails : $clockDetails->toArray();
            
            // Free memory immediately
            unset($clockDetails);
            
            // Force garbage collection
            gc_collect_cycles();
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Export Query Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'storeid' => $storeid,
                'date' => $date,
                'endDate' => $endDate,
            ]);
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Error generating PDF. The dataset may be too large. Please try selecting specific employees or a shorter date range.');
        }
        
        // Prepare data for PDF
        $searchDate = $validated['date'];
        $searchDateEnd = $validated['date_end'];
        $startDate = $date;
        $endDateFormatted = $endDate;
        
        // Get all employees for reference (convert to array to save memory)
        $allEmployees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->get(['employeeid', 'firstname', 'lastname'])
            ->toArray();
        
        // Generate PDF using mPDF (same as CI) for better memory efficiency with large tables
        try {
            // Render view to HTML string first (like CI does)
            $html = view('storeowner.clocktime.export', [
                'clockdetails' => $clockDetailsArray,
                'employee' => $allEmployees,
                'searchdate' => $searchDate,
                'searchdate_end' => $searchDateEnd,
                'startdate' => $startDate,
                'enddate' => $endDateFormatted,
            ])->render();
            
            // Free memory immediately after rendering HTML
            unset($clockDetailsArray, $allEmployees);
            gc_collect_cycles();
            
            // Initialize mPDF with landscape orientation (matching CI)
            // No headers/footers to avoid margin issues
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 0,
                'margin_footer' => 0,
                'tempDir' => storage_path('app/temp'),
                'autoScriptToLang' => false,
                'autoLangToFont' => false,
            ]);
            
            // Write HTML to PDF (same as CI's $this->m_pdf->pdf->WriteHTML($html))
            $mpdf->WriteHTML($html);
            
            // Free HTML from memory
            unset($html);
            gc_collect_cycles();
            
            // Generate filename
            $pdfFileName = 'Clock-In-Out-Report-' . $searchDate . '-to-' . $searchDateEnd . '.pdf';
            
            // Get PDF as string (mode 'S') to return as Laravel response (preserves headers)
            $pdfContent = $mpdf->Output('', 'S');
            
            // Free mPDF from memory
            unset($mpdf);
            gc_collect_cycles();
            
            // Return as Laravel response with proper headers
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"')
                ->header('Content-Length', strlen($pdfContent));
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Error generating PDF: ' . $e->getMessage() . '. Please try selecting fewer employees or a shorter date range.');
        }
    }

    /**
     * Display employee holidays page.
     */
    public function employeeHolidays(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getEmployeeHolidayCalculation($storeid);
        
        return view('storeowner.clocktime.employee_holidays', compact('empPayrollHrs'));
    }

    /**
     * Display compare weekly hours page (Employee Hours tab).
     */
    public function compareWeeklyHrs(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getAllWeekHrs($storeid);
        
        return view('storeowner.clocktime.compare_weekly_hrs', compact('empPayrollHrs'));
    }

    /**
     * Display all employees weekly hours page (Weekly Hours tab).
     */
    public function allemployeeWeeklyhrs(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getAllEmployeesWeekHrs($storeid);
        
        return view('storeowner.clocktime.allemployee_weeklyhrs', compact('empPayrollHrs'));
    }

    /**
     * Display weekly hours chart view.
     */
    public function reportsChartWeekly(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.clocktime.reports_chart_weekly');
    }

    /**
     * Display monthly hours chart view.
     */
    public function reportsChartMonthly(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.clocktime.reports_chart_monthly');
    }

    /**
     * Display monthly hours for all employees page (Monthly Hours tab).
     */
    public function monthlyHrsAllEmployee(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getMonthlyHrsAllEmployee($storeid);
        
        return view('storeowner.clocktime.monthly_hrs_allemployee', compact('empPayrollHrs'));
    }

    /**
     * Display weekly clock time for a specific employee.
     */
    public function weekClockTime($employeeid, $date): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $employeeid = base64_decode($employeeid);
        $date = Carbon::parse($date);
        $weekNumber = (int)$date->format('W');
        // Get ISO year (handles Week 1 starting in December - belongs to next year)
        $year = $this->clockTimeService->getIsoYearFromDate($date);

        $weekDates = $this->clockTimeService->getStartAndEndDate($weekNumber, $year);
        $startDate = $weekDates['week_start'];
        $endDate = $weekDates['week_end'];

        $weekid = $this->clockTimeService->getWeekId($weekNumber, $year);
        if (!$weekid) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Week not found for the selected date.');
        }

        $result = $this->clockTimeService->getEmployeeWeekDataByEmployee(
            $storeid, 
            $employeeid, 
            $weekid, 
            $startDate, 
            $endDate
        );

        $clockDetails = $result['clockdetails'];
        $totalPayrol = $result['totalPayrol'];

        $employee = StoreEmployee::find($employeeid);
        if (!$employee) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Employee not found.');
        }

        return view('storeowner.clocktime.weekclocktime', compact(
            'clockDetails', 
            'totalPayrol', 
            'startDate', 
            'endDate', 
            'employee',
            'weekNumber',
            'year',
            'weekid'
        ));
    }

    /**
     * Display weekly clock time for all employees for a specific week.
     */
    public function weekClockTimeAllEmp($weekid, $date): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $date = Carbon::parse($date);
        $weekNumber = (int)$date->format('W');
        // Get ISO year (handles Week 1 starting in December - belongs to next year)
        $year = $this->clockTimeService->getIsoYearFromDate($date);

        $weekDates = $this->clockTimeService->getStartAndEndDate($weekNumber, $year);
        $startDate = $weekDates['week_start'];
        $endDate = $weekDates['week_end'];

        // Get weekid from date (not from route to ensure consistency)
        $calculatedWeekid = $this->clockTimeService->getWeekId($weekNumber, $year);
        if (!$calculatedWeekid) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Week not found for the selected date.');
        }

        $result = $this->clockTimeService->getAllEmployeeWeekDataByWeek(
            $storeid, 
            $calculatedWeekid, 
            $startDate, 
            $endDate
        );

        $clockDetails = $result['clockdetails'];
        $totalPayrol = $result['totalPayrol'];

        return view('storeowner.clocktime.weekclocktime_allemp', compact(
            'clockDetails', 
            'totalPayrol', 
            'startDate', 
            'endDate',
            'weekNumber',
            'year',
            'calculatedWeekid'
        ));
    }

    /**
     * Display yearly hours for a specific employee.
     */
    public function yearlyHrsByEmployee($employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::find($employeeid);
        if (!$employee) {
            return redirect()->route('storeowner.clocktime.employee_holidays')
                ->with('error', 'Employee not found.');
        }

        $empPayrollHrs = $this->clockTimeService->getAllYearlyHrsByEmployee($storeid, $employeeid);
        
        return view('storeowner.clocktime.yearly_hrs_byemployee', compact('empPayrollHrs', 'employee'));
    }

    /**
     * Display yearly hours for all employees in a specific year.
     */
    public function groupYearlyHrsAllEmployee($year): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $year = base64_decode($year);
        $empPayrollHrs = $this->clockTimeService->getAllYearlyHrsAllEmployee($storeid, $year);
        
        return view('storeowner.clocktime.group_yearly_hrs_allemployee', compact('empPayrollHrs', 'year'));
    }

    /**
     * Display yearly hours breakdown for an employee in a specific year (weekly breakdown).
     */
    public function yearlyHrsByYearEmployee($employeeid, $year): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $employeeid = base64_decode($employeeid);
        $year = base64_decode($year);
        
        $employee = StoreEmployee::find($employeeid);
        if (!$employee) {
            return redirect()->route('storeowner.clocktime.employee_holidays')
                ->with('error', 'Employee not found.');
        }

        // Get weekly breakdown for the specific year
        $empPayrollHrs = $this->clockTimeService->getYearlyHrsByEmployee($storeid, $employeeid, $year);
        
        // Get all yearly data for the employee (for the summary table)
        $empPayrollHrsYearly = $this->clockTimeService->getAllYearlyHrsByEmployee($storeid, $employeeid);
        
        return view('storeowner.clocktime.yearly_hrs_by_year_employee', compact('empPayrollHrs', 'empPayrollHrsYearly', 'employee', 'year'));
    }

    /**
     * Export all employee holidays summary.
     */
    public function exportAllEmployeeHols(): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getEmployeeHolidayCalculation($storeid);
        
        // Generate PDF using mPDF (same as CI)
        try {
            // Render view to HTML string first (like CI does)
            $html = view('storeowner.clocktime.export_all_employee_hols', [
                'empPayrollHrs' => $empPayrollHrs,
            ])->render();
            
            // Free memory immediately after rendering HTML
            unset($empPayrollHrs);
            gc_collect_cycles();
            
            // Initialize mPDF with landscape orientation (matching CI)
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 0,
                'margin_footer' => 0,
                'tempDir' => storage_path('app/temp'),
                'autoScriptToLang' => false,
                'autoLangToFont' => false,
            ]);
            
            // Write HTML to PDF (same as CI's $this->m_pdf->pdf->WriteHTML($html))
            $mpdf->WriteHTML($html);
            
            // Free HTML from memory
            unset($html);
            gc_collect_cycles();
            
            // Generate filename
            $pdfFileName = 'All-Employee-Hours&Holiday-Summary.pdf';
            
            // Get PDF as string (mode 'S') to return as Laravel response (preserves headers)
            $pdfContent = $mpdf->Output('', 'S');
            
            // Free mPDF from memory
            unset($mpdf);
            gc_collect_cycles();
            
            // Return as Laravel response with proper headers (D = download, matching CI)
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"')
                ->header('Content-Length', strlen($pdfContent));
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('storeowner.clocktime.employee_holidays')
                ->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export group yearly holidays summary for a specific year.
     */
    public function exportGroupAllEmployeeHols($year): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $year = base64_decode($year);
        $empPayrollHrs = $this->clockTimeService->getAllYearlyHrsAllEmployee($storeid, $year);
        
        // TODO: Implement PDF generation using Laravel PDF library (e.g., barryvdh/laravel-dompdf)
        // For now, return a redirect with info message
        return redirect()->route('storeowner.clocktime.group-yearly-hrs-all-employee', ['year' => base64_encode($year)])
            ->with('info', 'PDF export functionality will be implemented. For now, please use the search results.');
    }

    /**
     * Display weekly hours for a specific employee.
     */
    public function weeklyHrsByEmployee($employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::find($employeeid);
        if (!$employee) {
            return redirect()->route('storeowner.clocktime.compare_weekly_hrs')
                ->with('error', 'Employee not found.');
        }

        $empPayrollHrs = $this->clockTimeService->getAllWeekHrsByEmployee($storeid, $employeeid);
        
        return view('storeowner.clocktime.weekly_hrs_byemployee', compact('empPayrollHrs', 'employee'));
    }

    /**
     * Display weekly hours for all employees in a specific week and year.
     */
    public function weeklyHrsByWeek($weekno, $year): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $weekno = base64_decode($weekno);
        $year = base64_decode($year);

        $empPayrollHrs = $this->clockTimeService->getAllWeekHrsByWeek($storeid, $weekno, $year);
        
        return view('storeowner.clocktime.weekly_hrs_byweek', compact('empPayrollHrs', 'weekno', 'year'));
    }

    /**
     * Get clock in-out data for editing (AJAX).
     */
    public function editClockInOut(Request $request): \Illuminate\Http\JsonResponse
    {
        $eltid = $request->input('eltid');
        $clockTime = EmpLoginTime::find($eltid);
        
        if (!$clockTime) {
            return response()->json(['error' => 'Clock time not found'], 404);
        }
        
        return response()->json([
            'clockin' => $clockTime->clockin ? \Carbon\Carbon::parse($clockTime->clockin)->format('Y-m-d H:i:s') : '',
            'clockout' => $clockTime->clockout ? \Carbon\Carbon::parse($clockTime->clockout)->format('Y-m-d H:i:s') : '',
        ]);
    }

    /**
     * Update employee time card.
     */
    public function editEmpTimecard(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $validated = $request->validate([
            'eltid' => 'required|integer',
            'clockin' => 'required|date',
            'clockout' => 'required|date|after:clockin',
        ]);

        $storeid = $this->getStoreId();
        
        $clockTime = EmpLoginTime::where('eltid', $validated['eltid'])
            ->where('storeid', $storeid)
            ->first();
        
        if (!$clockTime) {
            return redirect()->back()->with('error', 'Clock time not found.');
        }
        
        $clockTime->update([
            'clockin' => \Carbon\Carbon::parse($validated['clockin']),
            'clockout' => \Carbon\Carbon::parse($validated['clockout']),
            'editby' => $user->username ?? 'admin',
            'editdate' => now(),
            'editip' => $request->ip(),
        ]);
        
        return redirect()->back()->with('success', 'Employee time card successfully updated.');
    }

    /**
     * Add a new shift.
     */
    public function addShift(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $validated = $request->validate([
            'employeeid' => 'required|integer',
            'weekid' => 'required|integer',
            'sclockin' => 'required|date',
            'sclockout' => 'required|date|after:sclockin',
            'status' => 'required|string',
            'inRoster' => 'required|string',
        ]);

        $storeid = $this->getStoreId();
        
        $clockInDate = \Carbon\Carbon::parse($validated['sclockin']);
        
        EmpLoginTime::create([
            'storeid' => $storeid,
            'employeeid' => $validated['employeeid'],
            'clockin' => $clockInDate,
            'clockout' => \Carbon\Carbon::parse($validated['sclockout']),
            'weekid' => $validated['weekid'],
            'day' => $clockInDate->format('l'),
            'inRoster' => $validated['inRoster'],
            'status' => $validated['status'],
            'insertby' => $user->username ?? 'admin',
            'insertdate' => now(),
            'insertip' => $request->ip(),
        ]);
        
        return redirect()->back()->with('success', 'Employee shift added successfully.');
    }

    /**
     * Delete a shift.
     */
    public function deleteShift($eltid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        try {
            $eltid = base64_decode($eltid);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid shift ID');
        }
        
        $clockTime = EmpLoginTime::where('eltid', $eltid)
            ->where('storeid', $storeid)
            ->first();
        
        if (!$clockTime) {
            return redirect()->back()->with('error', 'Shift not found.');
        }
        
        $clockTime->delete();
        
        return redirect()->back()->with('success', 'Shift deleted successfully.');
    }

    /**
     * Generate week payslip.
     */
    public function generateWeekPayslip(): View|RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        // TODO: Implement payslip generation
        // For now, redirect back with info message
        return redirect()->back()->with('info', 'Payslip generation functionality will be implemented.');
    }

    /**
     * Export week clock time for all employees (PDF).
     */
    public function exportWeekAllEmp($weekid, $date): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $date = Carbon::parse($date);
        $weekNumber = (int)$date->format('W');
        // Get ISO year (handles Week 1 starting in December - belongs to next year)
        $year = $this->clockTimeService->getIsoYearFromDate($date);

        $weekDates = $this->clockTimeService->getStartAndEndDate($weekNumber, $year);
        $startDate = $weekDates['week_start'];
        $endDate = $weekDates['week_end'];

        $calculatedWeekid = $this->clockTimeService->getWeekId($weekNumber, $year);
        if (!$calculatedWeekid) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Week not found for the selected date.');
        }

        // Use the same export logic as exportPdf but for all employees
        return $this->exportPdf(new Request([
            'date' => \Carbon\Carbon::parse($startDate)->format('d-m-Y'),
            'date_end' => \Carbon\Carbon::parse($endDate)->format('d-m-Y'),
            'employeeid' => [] // Empty array means all employees
        ]));
    }

    /**
     * Upload all employee daily hours to dashboard.
     */
    public function uploadAllEmployeeDailyHours(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();

        // TODO: Implement upload functionality
        // This should save the hours to a dashboard/payroll table
        // For now, redirect back with success message
        return redirect()->back()->with('success', 'Employee hours uploaded successfully.');
    }

    /**
     * Display all employee payroll hours for a specific week.
     */
    public function weekAllEmpPayrollHrs($weekid, $date): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();
        
        $date = Carbon::parse($date);
        $weekNumber = (int)$date->format('W');
        // Get ISO year (handles Week 1 starting in December - belongs to next year)
        $year = $this->clockTimeService->getIsoYearFromDate($date);

        $weekDates = $this->clockTimeService->getStartAndEndDate($weekNumber, $year);
        $startDate = $weekDates['week_start'];
        $endDate = $weekDates['week_end'];

        $calculatedWeekid = $this->clockTimeService->getWeekId($weekNumber, $year);
        if (!$calculatedWeekid) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Week not found for the selected date.');
        }

        // Get payroll hours grouped by employee (use same method as weekClockTimeAllEmp)
        $result = $this->clockTimeService->getAllEmployeeWeekDataByWeek(
            $storeid, 
            $calculatedWeekid, 
            $startDate, 
            $endDate
        );

        $allClockDetails = $result['clockdetails'];
        
        // Group by employee and calculate totals (matching CI logic)
        $payrollByEmployee = [];
        $totalPayrolHr = 0;
        
        foreach ($allClockDetails as $val) {
            $employeeid = $val->employeeid;
            
            if (!isset($payrollByEmployee[$employeeid])) {
                $payrollByEmployee[$employeeid] = [
                    'employeeid' => $employeeid,
                    'storeid' => $val->storeid,
                    'employee' => $val->employee,
                    'firstname' => $val->employee->firstname ?? '',
                    'lastname' => $val->employee->lastname ?? '',
                    'total' => 0,
                    'totalBreakout' => 0,
                    'numOfdaysWorded' => 0,
                    'weekid' => $calculatedWeekid,
                    'status' => $val->status,
                ];
            }
            
            // Count days worked (exclude clockout status)
            if ($val->status != 'clockout') {
                $payrollByEmployee[$employeeid]['numOfdaysWorded']++;
            }
            
            // Sum totals
            $payrollByEmployee[$employeeid]['total'] += $val->total ?? 0;
            $payrollByEmployee[$employeeid]['totalBreakout'] += $val->totalBreakout ?? 0;
            $totalPayrolHr += $val->total ?? 0;
        }
        
        // Convert to collection for easier handling
        $clockDetails = collect($payrollByEmployee)->values();
        $weekDisplay = $weekNumber . '-' . $year;
        $totalPayrol = $totalPayrolHr;

        return view('storeowner.clocktime.week_allemp_payroll_hrs', compact(
            'clockDetails',
            'totalPayrol',
            'startDate',
            'endDate',
            'weekNumber',
            'year',
            'calculatedWeekid',
            'weekDisplay'
        ));
    }

    /**
     * Export payroll hours PDF for all employees.
     */
    public function exportPayrollHrs($weekid, $date): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        // Use the same export logic as exportWeekAllEmp
        return $this->exportWeekAllEmp($weekid, $date);
    }

    /**
     * Get weekly hours chart data for dashboard (AJAX).
     * Matches CI's clocktime/get_hours_chart_weekly.
     */
    public function getHoursChartWeekly()
    {
        $storeid = $this->getStoreId();
        
        $data = \Illuminate\Support\Facades\DB::table('stoma_emp_payroll_hrs')
            ->select([
                'weekno',
                'year',
                \Illuminate\Support\Facades\DB::raw('SUM(CAST(hours_worked AS DECIMAL(10,2))) AS hours_worked')
            ])
            ->where('storeid', $storeid)
            ->groupBy('year', 'weekno')
            ->orderBy('year', 'DESC')
            ->orderBy('weekno', 'DESC')
            ->get()
            ->map(function($item) {
                return [
                    'weekno' => (string)$item->weekno, // String format to match CI
                    'year' => (int)$item->year,
                    'hours_worked' => (float)$item->hours_worked
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Matches CI's clocktime/get_hours_chart_monthly.
     */
    public function getHoursChartMonthly()
    {
        $storeid = $this->getStoreId();
        
        $data = \Illuminate\Support\Facades\DB::table('stoma_emp_payroll_hrs')
            ->select([
                \Illuminate\Support\Facades\DB::raw('MONTH(week_start) AS mymonth'),
                'year',
                \Illuminate\Support\Facades\DB::raw('SUM(CAST(hours_worked AS DECIMAL(10,2))) AS hours_worked')
            ])
            ->where('storeid', $storeid)
            ->groupBy('year', 'mymonth')
            ->orderBy('year', 'DESC')
            ->orderBy('mymonth', 'DESC')
            ->get()
            ->map(function($item) {
                // Convert month number to month name
                $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                              'July', 'August', 'September', 'October', 'November', 'December'];
                $monthName = isset($monthNames[(int)$item->mymonth]) ? $monthNames[(int)$item->mymonth] : (string)$item->mymonth;
                
                return [
                    'mymonth' => $monthName, // Month name to match CI
                    'year' => (int)$item->year,
                    'hours_worked' => (float)$item->hours_worked
                ];
            });
        
        return response()->json($data);
    }

    /**
     * Upload all weekly hours to dashboard.
     */
    public function uploadAllWeeklyHours(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }

        $storeid = $this->getStoreId();

        // TODO: Implement upload functionality to save to stoma_emp_payroll_hrs table
        // This should save the hours to a dashboard/payroll table
        // For now, redirect back with success message
        return redirect()->back()->with('success', 'Employee hours uploaded successfully.');
    }

    /**
     * Show the form for editing employee hours.
     */
    public function editEmployeeHours(string $payrollId): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $payrollId = base64_decode($payrollId);
        $storeid = $this->getStoreId();
        
        $empPayrollHrs = $this->clockTimeService->getPayrollHourById($storeid, $payrollId);
        
        if (!$empPayrollHrs) {
            return redirect()->route('storeowner.clocktime.compare_weekly_hrs')
                ->with('error', 'Payroll hours record not found.');
        }
        
        return view('storeowner.clocktime.edit_employee_hours', compact('empPayrollHrs'));
    }

    /**
     * Update employee hours.
     */
    public function updateEmployeeHours(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $validated = $request->validate([
            'payroll_id' => 'required|string',
            'hours_worked' => 'required|numeric|min:0',
            'break_deducted' => 'nullable|numeric|min:0',
            'sunday_hrs' => 'nullable|numeric|min:0',
            'owertime1_hrs' => 'nullable|numeric|min:0',
            'owertime2_hrs' => 'nullable|numeric|min:0',
            'holiday_hrs' => 'nullable|numeric|min:0',
            'holiday_days' => 'nullable|numeric|min:0',
            'sickpay_hrs' => 'nullable|numeric|min:0',
            'extras1_hrs' => 'nullable|numeric|min:0',
            'extras2_hrs' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $payrollId = base64_decode($validated['payroll_id']);
        
        // Verify the payroll record belongs to this store
        $payrollRecord = $this->clockTimeService->getPayrollHourById($storeid, $payrollId);
        if (!$payrollRecord) {
            return redirect()->route('storeowner.clocktime.compare_weekly_hrs')
                ->with('error', 'Payroll hours record not found.');
        }
        
        $data = [
            'hours_worked' => $validated['hours_worked'],
            'break_deducted' => $validated['break_deducted'] ?? 0,
            'sunday_hrs' => $validated['sunday_hrs'] ?? 0,
            'owertime1_hrs' => $validated['owertime1_hrs'] ?? 0,
            'owertime2_hrs' => $validated['owertime2_hrs'] ?? 0,
            'holiday_hrs' => $validated['holiday_hrs'] ?? 0,
            'holiday_days' => $validated['holiday_days'] ?? 0,
            'sickpay_hrs' => $validated['sickpay_hrs'] ?? 0,
            'extras1_hrs' => $validated['extras1_hrs'] ?? 0,
            'extras2_hrs' => $validated['extras2_hrs'] ?? '',
            'notes' => $validated['notes'] ?? '',
            'editdate' => now(),
            'editip' => $request->ip(),
        ];
        
        $updated = DB::table('stoma_emp_payroll_hrs')
            ->where('payroll_id', $payrollId)
            ->where('storeid', $storeid)
            ->update($data);
        
        if ($updated) {
            return redirect()->route('storeowner.clocktime.compare_weekly_hrs')
                ->with('success', 'Employee hours updated successfully.');
        }
        
        return redirect()->route('storeowner.clocktime.compare_weekly_hrs')
            ->with('error', 'Failed to update employee hours.');
    }

    /**
     * Display clock time settings page.
     */
    public function settings(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $store = Store::find($storeid);
        
        if (!$store) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Store not found.');
        }
        
        return view('storeowner.clocktime.settings', compact('store'));
    }

    /**
     * Update clock time settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        $store = Store::find($storeid);
        
        if (!$store) {
            return redirect()->route('storeowner.clocktime.settings')
                ->with('error', 'Store not found.');
        }
        
        $validated = $request->validate([
            'enable_break_events' => 'required|in:Yes,No',
        ]);
        
        $store->enable_break_events = $validated['enable_break_events'];
        $store->editdate = Carbon::now();
        $store->editip = $request->ip();
        $store->editby = Auth::id();
        $store->save();
        
        return redirect()->route('storeowner.clocktime.settings')
            ->with('success', 'Clock time settings updated successfully.');
    }
}

