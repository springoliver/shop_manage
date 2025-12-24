<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmpLoginTime;
use App\Models\Store;
use App\Models\StoreOwner;
use App\Models\Week;
use App\Models\Year;
use App\Models\Roster;
use App\Models\BreakEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class WebServiceController extends Controller
{
    /**
     * Add CORS headers to response
     */
    private function addCorsHeaders(JsonResponse $response): JsonResponse
    {
        return $response->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Store Login endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function storeLogin(Request $request): JsonResponse
    {

        $email = $request->input('email');
        $password = $request->input('password');

        // Laravel best practice: Use filled() helper or explicit checks
        if (!filled($email) || !filled($password)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Enter all values...'
            ]));
        }

        // Get store owner by email first
        $storeOwner = StoreOwner::where('emailid', $email)->first();
        
        // Verify password using Laravel's Hash (password is hashed in Laravel, not base64 encoded)
        if (!$storeOwner || !Hash::check($password, $storeOwner->password)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'No record found.!'
            ]));
        }

        // Get stores for this owner
        $stores = Store::where('storeownerid', $storeOwner->ownerid)
            ->select('storeid', 'store_name')
            ->get();

        // Laravel best practice: Use isNotEmpty() instead of count() > 0
        if ($stores->isNotEmpty()) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'success',
                'msg' => 'success',
                'data' => $stores->toArray()
            ]));
        }

        return $this->addCorsHeaders(response()->json([
            'status' => 'fail',
            'msg' => 'No record found.!'
        ]));
    }

    /**
     * Get Clock Details endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getClockDetails(Request $request): JsonResponse
    {
        $empLogCode = $request->input('emp_log_code');
        $storeid = $request->input('storeid');

        // ========== INPUT VALIDATION ==========
        // Laravel best practice: Use filled() helper
        if (!filled($empLogCode) || !filled($storeid)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee login code and store ID are required!'
            ]));
        }

        // Validate store exists
        $store = Store::where('storeid', $storeid)->first();
        if (!$store) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store not found!'
            ]));
        }

        // ========== BUSINESS RULE VALIDATION ==========
        // Check employee detail by login code
        $employee = Employee::where('emplogin_code', $empLogCode)
            ->where('storeid', $storeid)
            ->with('store')
            ->first();

        if (!$employee) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Incorrect login code. Please check your employee login code!'
            ]));
        }

        $storeid = $employee->storeid;
        $empid = $employee->employeeid;
        $empLogCode = $employee->emplogin_code;
        $empName = $employee->firstname . ' ' . $employee->lastname;
        $profilePhoto = $employee->profile_photo;
        $storeName = $employee->store->store_name ?? '';

        // Check if employee has roster
        $roster = Roster::where('storeid', $storeid)
            ->where('employeeid', $empid)
            ->first();
        $rosterType = $roster ? 'Yes' : 'No';

        // Check clock time details - get latest record
        $clockTime = EmpLoginTime::where('storeid', $storeid)
            ->where('employeeid', $empid)
            ->orderBy('eltid', 'DESC')
            ->first();

        // If status == "clockin" or no record, next action is clockin (they're clocked out)
        $clockType = 'clockin'; // Default: no record or status is 'clockin'
        if ($clockTime && $clockTime->status == 'clockout') {
            $clockType = 'clockout'; // They're clocked in, can clock out
        }

        // Assuming profile photos are in storage/app/public/profile-photos or similar
        $basePath = config('filesystems.disks.public.url', '/storage');
        $path = url($basePath . '/profile-photos/' . $profilePhoto);
        $finalPath = str_replace('/ws', '', $path);

        // Get store to check break events setting
        $store = Store::find($storeid);
        $breakEventsEnabled = $store && $store->enable_break_events == 'Yes';

        $resultArray = [
            'storeid' => $storeid,
            'empid' => $empid,
            'empname' => $empName,
            'emp_log_code' => $empLogCode,
            'roster_type' => $rosterType,
            'clock_type' => $clockType,
            'pic_path' => $finalPath,
            'store_name' => $storeName,
            'break_events_enabled' => $breakEventsEnabled ? 'Yes' : 'No'
        ];

        // Add eltid if clocked in
        if ($clockTime && $clockTime->status == 'clockout') {
            $resultArray['eltid'] = (string)$clockTime->eltid;
        }

        return $this->addCorsHeaders(response()->json([
            'status' => 'success',
            'data' => $resultArray
        ]));
    }

    /**
     * Insert Clock Details endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function insertClockDetails(Request $request): JsonResponse
    {
        $time = $request->input('time');
        $clockType = $request->input('clock_type');
        $storeid = $request->input('storeid');
        $empid = $request->input('empid');
        $empLogCode = $request->input('emp_log_code'); // For offline sync with employee code only
        $rosterType = $request->input('roster_type');
        $day = $request->input('day');

        // ========== INPUT VALIDATION ==========
        // Laravel best practice: Use filled() helper for validation
        // Allow storeid to be optional if emp_log_code is provided (will be resolved from employee)
        if (!filled($time) || !filled($clockType) || (!filled($empid) && !filled($empLogCode)) || !filled($rosterType) || !filled($day)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'All fields are required! (empid or emp_log_code must be provided)'
            ]));
        }
        
        // If emp_log_code is provided but empid is not, resolve employee code to empid
        if (filled($empLogCode) && !filled($empid)) {
            // If storeid is provided, use it to find employee
            // If storeid is not provided, try to find employee by code only (assuming code is unique or we take first match)
            if (filled($storeid)) {
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)
                    ->where('storeid', $storeid)
                    ->first();
            } else {
                // No storeid provided - find employee by code only
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)->first();
                if ($employeeByCode) {
                    // Get storeid from employee record
                    $storeid = $employeeByCode->storeid;
                }
            }
            
            if (!$employeeByCode) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Employee not found with login code: ' . $empLogCode
                ]));
            }
            
            $empid = $employeeByCode->employeeid;
        }
        
        // Validate storeid is now available (either from input or resolved from employee)
        if (!filled($storeid)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store ID is required!'
            ]));
        }

        // Validate clock type
        if (!in_array($clockType, ['clockin', 'clockout'])) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Invalid clock type!'
            ]));
        }

        // Parse time - accept both "dd-MM-yyyy HH:mm:ss" (new) and "HH:mm:ss" (legacy)
        $clientDateTime = null;
        try {
            $clientDateTime = Carbon::createFromFormat('d-m-Y H:i:s', $time);
        } catch (\Exception $e) {
            // Fallback to legacy format (HH:mm:ss) - uses server's current date
            try {
                $clientDateTime = Carbon::today()->setTimeFromTimeString($time);
            } catch (\Exception $e2) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Invalid time format! Expected: dd-MM-yyyy HH:mm:ss or HH:mm:ss'
                ]));
            }
        }

        // ========== BUSINESS RULE VALIDATION ==========
        // Validate employee exists and belongs to store
        $employee = Employee::where('employeeid', $empid)
            ->where('storeid', $storeid)
            ->first();

        if (!$employee) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee not found or does not belong to this store!'
            ]));
        }

        // Validate store exists
        $store = Store::where('storeid', $storeid)->first();
        if (!$store) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store not found!'
            ]));
        }

        // ========== DETERMINE ROSTER STATUS ==========
        // If roster_type is "No" (from offline sync) or not provided, check if employee actually has a roster
        // This ensures offline events appear in the week view (which filters by inRoster = 'Yes')
        if ($rosterType == 'No' || !filled($rosterType)) {
            $roster = Roster::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->first();
            $rosterType = $roster ? 'Yes' : 'No';
        }

        // Parse date and get week number and year
        $date = $clientDateTime;
        $weekNumber = (int)$date->format('W'); // Week number (1-53)
        $year = (string)$date->format('Y'); // Year as string (matching ClockTimeService)

        // Get or create year record (year is stored as string in database)
        $yearRecord = Year::firstOrCreate(
            ['year' => $year]
        );

        // Get or create week record
        $weekRecord = Week::firstOrCreate(
            [
                'weeknumber' => $weekNumber,
                'yearid' => $yearRecord->yearid
            ]
        );

        if ($clockType == 'clockin') {
            // ========== DUPLICATE CHECK: Prevent duplicate clock-in ==========
            // Check if employee already has an active clock-in (status = 'clockout' means they're clocked in)
            $activeClockIn = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('status', 'clockout') // Status 'clockout' means they're clocked in (waiting for clock-out)
                ->whereNull('clockout') // No clock-out time yet
                ->orderBy('eltid', 'DESC')
                ->first();

            if ($activeClockIn) {
                // Check if it's a duplicate on the same day
                $clockInDate = date('Y-m-d', strtotime($time));
                $existingClockInDate = date('Y-m-d', strtotime($activeClockIn->clockin));
                
                if ($clockInDate == $existingClockInDate) {
                    return $this->addCorsHeaders(response()->json([
                        'status' => 'fail',
                        'msg' => 'You are already clocked in! Please clock out first.'
                    ]));
                }
                
                // If different day, allow but log warning (edge case)
                // This handles cases where employee forgot to clock out previous day
            }

            // ========== DUPLICATE CHECK: Prevent multiple clock-ins on same day ==========
            $clockInDateTime = $clientDateTime->format('Y-m-d H:i:s');
            $clockInDate = $clientDateTime->format('Y-m-d');
            
            // Check for existing clock-in on the same day (even if clocked out)
            $sameDayClockIn = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->whereDate('clockin', $clockInDate)
                ->where('inRoster', $rosterType)
                ->first();

            if ($sameDayClockIn && $sameDayClockIn->status == 'clockout' && is_null($sameDayClockIn->clockout)) {
                // Already clocked in today and not clocked out
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'You have already clocked in today. Please clock out first.'
                ]));
            }

            // Insert clock in record
            $clockInDateTime = $clientDateTime->format('Y-m-d H:i:s');
            
            $insertArray = [
                'storeid' => $storeid,
                'employeeid' => $empid,
                'clockin' => $clockInDateTime,
                'inRoster' => $rosterType,
                'day' => $day,
                'status' => 'clockout', // Status 'clockout' means next action is clock-out
                'weekid' => $weekRecord->weekid,
                'insertdate' => Carbon::now(),
            ];

            // Laravel's create() returns the model instance, which is truthy if successful
            try {
                $res = EmpLoginTime::create($insertArray);

            if ($res) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'success',
                    'msg' => 'Clock in Successfully',
                    'clockin' => $clientDateTime->format('H:i:s'),
                    'clockout' => '00:00:00',
                    'eltid' => $res->eltid,
                    'empid' => $empid // Return empid for offline sync to store it
                ]));
            } else {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Failed to clock in. Please try again.'
                ]));
            }
            } catch (\Exception $e) {
                // Handle database constraint violations (duplicate key, etc.)
                \Log::error('Clock-in error: ' . $e->getMessage());
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Database error. Please try again.'
                ]));
            }
        } else if ($clockType == 'clockout') {
            // ========== DUPLICATE CHECK: Prevent duplicate clock-out ==========
            $clockOutDateTime = $clientDateTime->format('Y-m-d H:i:s');
            $clockOutDate = $clientDateTime->format('Y-m-d');

            // Try to find clock-in record with multiple strategies for better matching
            // Strategy 1: Exact match (storeid, employeeid, inRoster, status)
            $clockInRecord = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('inRoster', $rosterType)
                ->where('status', 'clockout') // Status 'clockout' means they're clocked in
                ->whereNull('clockout') // Ensure it doesn't already have a clockout time
                ->orderBy('eltid', 'DESC') // Get latest matching record
                ->first();

            // Strategy 2: If not found, try without roster_type match (in case it changed)
            if (!$clockInRecord) {
                $clockInRecord = EmpLoginTime::where('storeid', $storeid)
                    ->where('employeeid', $empid)
                    ->where('status', 'clockout')
                    ->whereNull('clockout') // Ensure it doesn't already have a clockout time
                    ->orderBy('eltid', 'DESC') // Get latest matching record
                    ->first();
            }

            // Strategy 3: If still not found, try matching by same day (for offline sync cases)
            if (!$clockInRecord) {
                $clockOutDate = $clientDateTime->format('Y-m-d');
                $clockInRecord = EmpLoginTime::where('storeid', $storeid)
                    ->where('employeeid', $empid)
                    ->where('status', 'clockout')
                    ->whereNull('clockout')
                    ->whereDate('clockin', $clockOutDate)
                    ->orderBy('eltid', 'DESC')
                    ->first();
            }

            // ========== VALIDATION: Clock-in record must exist ==========
            if (!$clockInRecord) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Clock-in record not found. Please clock in first.'
                ]));
            }

            // ========== DUPLICATE CHECK: Prevent clocking out twice on same record ==========
            if (!is_null($clockInRecord->clockout)) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'You have already clocked out for this session.'
                ]));
            }

            // ========== VALIDATION: Clock-out time must be after clock-in time ==========
            $clockInTime = strtotime($clockInRecord->clockin);
            $clockOutTime = $clientDateTime->timestamp;
            
            if ($clockOutTime <= $clockInTime) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Clock-out time must be after clock-in time!'
                ]));
            }

            // ========== DUPLICATE CHECK: Prevent multiple clock-outs on same day ==========
            $existingClockOut = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->whereDate('clockout', $clockOutDate)
                ->whereNotNull('clockout')
                ->where('eltid', '!=', $clockInRecord->eltid)
                ->first();

            if ($existingClockOut) {
                // Check if it's within a reasonable time window (e.g., 1 minute) - might be duplicate request
                $timeDiff = abs(strtotime($existingClockOut->clockout) - $clockOutTime);
                if ($timeDiff < 60) { // Less than 1 minute difference
                    return $this->addCorsHeaders(response()->json([
                        'status' => 'fail',
                        'msg' => 'Duplicate clock-out detected. You have already clocked out today.'
                    ]));
                }
            }

            // Update clock out record
            $updateArray = [
                'clockout' => $clockOutDateTime,
                'status' => 'clockin' // Next action will be clockin
            ];

            try {
                $res = $clockInRecord->update($updateArray);
                // Refresh the record to get updated data
                $clockInRecord->refresh();

            if ($res) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'success',
                    'msg' => 'You have been clocked out successfully',
                    'clockin' => date('H:i:s', strtotime($clockInRecord->clockin)),
                    'clockout' => $clientDateTime->format('H:i:s')
                ]));
                } else {
                    return $this->addCorsHeaders(response()->json([
                        'status' => 'fail',
                        'msg' => 'Failed to clock out. Please try again.'
                    ]));
                }
            } catch (\Exception $e) {
                // Handle database constraint violations
                \Log::error('Clock-out error: ' . $e->getMessage());
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Database error. Please try again.'
                ]));
            }
        }

        return $this->addCorsHeaders(response()->json([
            'status' => 'fail',
            'msg' => 'Invalid clock type!'
        ]));
    }

    /**
     * Check Clock In-Out Module endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkClockInOutModule(Request $request): JsonResponse
    {
        $storeid = $request->input('storeid');
        $currentDate = $request->input('current_date');

        // Check if clock in-out module is installed (check if there are any clock records)
        $hasRecords = EmpLoginTime::where('storeid', $storeid)
            ->exists();

        if ($hasRecords) {
            return $this->addCorsHeaders(response()->json([
                'install' => 'yes',
                'msg' => 'Clock in-out module has been installed'
            ]));
        } else {
            return $this->addCorsHeaders(response()->json([
                'install' => 'no',
                'msg' => 'Please activate clock in module from your Maximanage.com account. Visit now http://www.maximanage.com to join and activate.'
            ]));
        }
    }

    /**
     * Break Start endpoint
     * Records when an employee starts a break during their shift
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function breakStart(Request $request): JsonResponse
    {
        $storeid = $request->input('storeid');
        $empid = $request->input('empid');
        $empLogCode = $request->input('emp_log_code'); // For offline sync with employee code only
        $time = $request->input('time');
        $eltid = $request->input('eltid'); // Optional: specific login time record ID

        // ========== INPUT VALIDATION ==========
        if ((!filled($empid) && !filled($empLogCode)) || !filled($time)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee ID (or employee code) and time are required!'
            ]));
        }
        
        // If emp_log_code is provided but empid is not, resolve employee code to empid
        if (filled($empLogCode) && !filled($empid)) {
            // If storeid is provided, use it to find employee
            // If storeid is not provided, find employee by code only (assuming code is unique)
            if (filled($storeid)) {
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)
                    ->where('storeid', $storeid)
                    ->first();
            } else {
                // No storeid provided - find employee by code only (employee code is unique)
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)->first();
                if ($employeeByCode) {
                    // Get storeid from employee record
                    $storeid = $employeeByCode->storeid;
                }
            }
            
            if (!$employeeByCode) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Employee not found with login code: ' . $empLogCode
                ]));
            }
            
            $empid = $employeeByCode->employeeid;
        }
        
        // Validate storeid is now available (either from input or resolved from employee)
        if (!filled($storeid)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store ID is required!'
            ]));
        }

        // Parse time - accept both "dd-MM-yyyy HH:mm:ss" (new) and "HH:mm:ss" (legacy)
        $clientDateTime = null;
        try {
            $clientDateTime = Carbon::createFromFormat('d-m-Y H:i:s', $time);
        } catch (\Exception $e) {
            try {
                $clientDateTime = Carbon::today()->setTimeFromTimeString($time);
            } catch (\Exception $e2) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Invalid time format! Expected: dd-MM-yyyy HH:mm:ss or HH:mm:ss'
                ]));
            }
        }

        // ========== BUSINESS RULE VALIDATION ==========
        // Validate employee exists and belongs to store
        $employee = Employee::where('employeeid', $empid)
            ->where('storeid', $storeid)
            ->first();

        if (!$employee) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee not found or does not belong to this store!'
            ]));
        }

        // Find the correct clock-in record for this break
        // The break must occur during an active clock-in session
        // Find record where break_start time falls between clockin and clockout (or before clockout if not clocked out)
        $loginTimeRecord = null;
        
        // First, try to find by eltid if provided (as a hint, but verify it's correct)
        if (filled($eltid)) {
            $loginTimeRecord = EmpLoginTime::where('eltid', $eltid)
                ->where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('clockin', '<=', $clientDateTime->format('Y-m-d H:i:s')) // Break must be after clock-in
                ->where(function($query) use ($clientDateTime) {
                    $query->whereNull('clockout') // Not clocked out yet
                          ->orWhere('clockout', '>=', $clientDateTime->format('Y-m-d H:i:s')); // Or clock-out is after break start
                })
                ->first();
        }
        
        // If not found by eltid (or eltid not provided), find by time range
        if (!$loginTimeRecord) {
            $loginTimeRecord = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('clockin', '<=', $clientDateTime->format('Y-m-d H:i:s')) // Break must be after clock-in
                ->where(function($query) use ($clientDateTime) {
                    $query->whereNull('clockout') // Not clocked out yet
                          ->orWhere('clockout', '>=', $clientDateTime->format('Y-m-d H:i:s')); // Or clock-out is after break start
                })
                ->orderBy('eltid', 'DESC') // Get latest matching record
                ->first();
        }

        if (!$loginTimeRecord) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'You must be clocked in to start a break!'
            ]));
        }

        // ========== DUPLICATE CHECK: Prevent starting break if already on break ==========
        $activeBreak = BreakEvent::where('eltid', $loginTimeRecord->eltid)
            ->where('status', 'active')
            ->whereNull('break_end')
            ->first();

        if ($activeBreak) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'You are already on a break! Please end your current break first.'
            ]));
        }

        // Validate break start time is after clock-in time
        // Use client's datetime (already parsed above)
        $clockInDateTime = Carbon::parse($loginTimeRecord->clockin);
        $breakStartDateTime = $clientDateTime; // Use client's date and time
        
        // Validate break start is after clock-in
        if ($breakStartDateTime->lt($clockInDateTime)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Break start time must be after clock-in time!'
            ]));
        }

        // Create break event
        try {
            $breakEvent = BreakEvent::create([
                'eltid' => $loginTimeRecord->eltid,
                'storeid' => $storeid,
                'employeeid' => $empid,
                'break_start' => $breakStartDateTime->format('Y-m-d H:i:s'),
                'break_end' => null,
                'break_duration' => null,
                'status' => 'active',
                'insertdate' => Carbon::now(),
                'insertip' => $request->ip(),
            ]);

            return $this->addCorsHeaders(response()->json([
                'status' => 'success',
                'msg' => 'Break started successfully',
                'breakid' => $breakEvent->breakid,
                'break_start' => $breakStartDateTime->format('H:i:s'),
            ]));
        } catch (\Exception $e) {
            \Log::error('Break start error: ' . $e->getMessage());
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Failed to start break. Please try again.'
            ]));
        }
    }

    /**
     * Break End endpoint
     * Records when an employee ends their break and returns to work
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function breakEnd(Request $request): JsonResponse
    {
        $storeid = $request->input('storeid');
        $empid = $request->input('empid');
        $empLogCode = $request->input('emp_log_code'); // For offline sync with employee code only
        $time = $request->input('time');
        $breakid = $request->input('breakid'); // Optional: specific break event ID

        // ========== INPUT VALIDATION ==========
        if ((!filled($empid) && !filled($empLogCode)) || !filled($time)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee ID (or employee code) and time are required!'
            ]));
        }
        
        // If emp_log_code is provided but empid is not, resolve employee code to empid
        if (filled($empLogCode) && !filled($empid)) {
            // If storeid is provided, use it to find employee
            // If storeid is not provided, find employee by code only (assuming code is unique)
            if (filled($storeid)) {
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)
                    ->where('storeid', $storeid)
                    ->first();
            } else {
                // No storeid provided - find employee by code only (employee code is unique)
                $employeeByCode = Employee::where('emplogin_code', $empLogCode)->first();
                if ($employeeByCode) {
                    // Get storeid from employee record
                    $storeid = $employeeByCode->storeid;
                }
            }
            
            if (!$employeeByCode) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Employee not found with login code: ' . $empLogCode
                ]));
            }
            
            $empid = $employeeByCode->employeeid;
        }
        
        // Validate storeid is now available (either from input or resolved from employee)
        if (!filled($storeid)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store ID is required!'
            ]));
        }

        // ========== CHECK IF BREAK EVENTS ARE ENABLED ==========
        $store = Store::find($storeid);
        if (!$store || $store->enable_break_events != 'Yes') {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Break events are disabled for this store. Please contact your administrator.'
            ]));
        }

        // Parse time - accept both "dd-MM-yyyy HH:mm:ss" (new) and "HH:mm:ss" (legacy)
        $clientDateTime = null;
        try {
            $clientDateTime = Carbon::createFromFormat('d-m-Y H:i:s', $time);
        } catch (\Exception $e) {
            try {
                $clientDateTime = Carbon::today()->setTimeFromTimeString($time);
            } catch (\Exception $e2) {
                return $this->addCorsHeaders(response()->json([
                    'status' => 'fail',
                    'msg' => 'Invalid time format! Expected: dd-MM-yyyy HH:mm:ss or HH:mm:ss'
                ]));
            }
        }

        // ========== BUSINESS RULE VALIDATION ==========
        // Validate employee exists and belongs to store
        $employee = Employee::where('employeeid', $empid)
            ->where('storeid', $storeid)
            ->first();

        if (!$employee) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Employee not found or does not belong to this store!'
            ]));
        }

        // Find active break event
        $breakEvent = null;
        if (filled($breakid)) {
            $breakEvent = BreakEvent::where('breakid', $breakid)
                ->where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('status', 'active')
                ->whereNull('break_end')
                ->first();
        } else {
            // Find latest active break event
            $breakEvent = BreakEvent::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('status', 'active')
                ->whereNull('break_end')
                ->orderBy('breakid', 'DESC')
                ->first();
        }

        if (!$breakEvent) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'No active break found! Please start a break first.'
            ]));
        }

        // Validate break end time is after break start time
        $breakStartDateTime = Carbon::parse($breakEvent->break_start);
        $breakEndDateTime = $clientDateTime;
        
        if ($breakEndDateTime->lte($breakStartDateTime)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Break end time must be after break start time!'
            ]));
        }
        
        $breakStartTime = $breakStartDateTime->timestamp;
        $breakEndTime = $breakEndDateTime->timestamp;

        // Calculate break duration in minutes (round to nearest minute)
        $breakDuration = round(($breakEndTime - $breakStartTime) / 60);
        
        // Ensure minimum duration is at least 1 minute if times are different
        if ($breakDuration < 1 && $breakEndTime > $breakStartTime) {
            $breakDuration = 1;
        }

        // Update break event
        try {
            $breakEvent->update([
                'break_end' => $breakEndDateTime->format('Y-m-d H:i:s'),
                'break_duration' => $breakDuration,
                'status' => 'completed',
            ]);

            return $this->addCorsHeaders(response()->json([
                'status' => 'success',
                'msg' => 'Break ended successfully',
                'breakid' => $breakEvent->breakid,
                'break_start' => $breakStartDateTime->format('H:i:s'),
                'break_end' => $breakEndDateTime->format('H:i:s'),
                'break_duration' => $breakDuration, // in minutes
            ]));
        } catch (\Exception $e) {
            \Log::error('Break end error: ' . $e->getMessage());
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Failed to end break. Please try again.'
            ]));
        }
    }

    /**
     * Get Active Break Status endpoint
     * Returns current break status for an employee
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getBreakStatus(Request $request): JsonResponse
    {
        $storeid = $request->input('storeid');
        $empid = $request->input('empid');
        $eltid = $request->input('eltid'); // Optional: specific login time record ID

        // ========== INPUT VALIDATION ==========
        if (!filled($storeid) || !filled($empid)) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'fail',
                'msg' => 'Store ID and Employee ID are required!'
            ]));
        }

        // Find active clock-in record for break status check
        // This is used for checking if employee is on break, so we look for active clock-in
        $loginTimeRecord = null;
        if (filled($eltid)) {
            $loginTimeRecord = EmpLoginTime::where('eltid', $eltid)
                ->where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('status', 'clockout')
                ->whereNull('clockout')
                ->first();
        } else {
            $loginTimeRecord = EmpLoginTime::where('storeid', $storeid)
                ->where('employeeid', $empid)
                ->where('status', 'clockout')
                ->whereNull('clockout')
                ->orderBy('eltid', 'DESC')
                ->first();
        }

        if (!$loginTimeRecord) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'success',
                'on_break' => false,
                'break_events_enabled' => true,
                'msg' => 'Not clocked in'
            ]));
        }

        // Find active break
        $activeBreak = BreakEvent::where('eltid', $loginTimeRecord->eltid)
            ->where('status', 'active')
            ->whereNull('break_end')
            ->first();

        if ($activeBreak) {
            return $this->addCorsHeaders(response()->json([
                'status' => 'success',
                'on_break' => true,
                'break_events_enabled' => true,
                'breakid' => $activeBreak->breakid,
                'break_start' => date('H:i:s', strtotime($activeBreak->break_start)),
                'break_start_full' => $activeBreak->break_start,
            ]));
        }

        return $this->addCorsHeaders(response()->json([
            'status' => 'success',
            'on_break' => false,
            'break_events_enabled' => true,
            'msg' => 'Not on break'
        ]));
    }
}

