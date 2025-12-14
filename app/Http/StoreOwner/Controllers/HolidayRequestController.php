<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HolidayRequest;
use App\Models\StoreEmployee;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HolidayRequestController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Time Off Request module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Time Off Request')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of holiday requests.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $holidayRequests = HolidayRequest::with('employee')
            ->where('storeid', $storeid)
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Deactivate');
            })
            ->orderBy('requestid', 'DESC')
            ->get();
        
        return view('storeowner.holidayrequest.index', compact('holidayRequests'));
    }

    /**
     * Show the form for creating a new holiday request.
     */
    public function create(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get active employees (excluding those in emp_payroll_ire_employee_settings if that table exists)
        // For now, just get all active employees
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        return view('storeowner.holidayrequest.create', compact('employees'));
    }

    /**
     * Store a newly created holiday request.
     */
    public function store(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'employeeid' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $storeid = $this->getStoreId();
        
        HolidayRequest::create([
            'storeid' => $storeid,
            'employeeid' => $validated['employeeid'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Pending',
            'insertdatetime' => now(),
            'insertip' => $request->ip(),
        ]);
        
        return redirect()->route('storeowner.holidayrequest.index')
            ->with('success', 'Request details added successfully.');
    }

    /**
     * Display the specified holiday request.
     */
    public function show(string $requestid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $requestid = base64_decode($requestid);
        $holidayRequest = HolidayRequest::with('employee')
            ->findOrFail($requestid);
        
        return view('storeowner.holidayrequest.view', compact('holidayRequest'));
    }

    /**
     * Show the form for editing the specified holiday request.
     */
    public function edit(string $requestid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $requestid = base64_decode($requestid);
        $holidayRequest = HolidayRequest::with('employee')->findOrFail($requestid);
        
        $storeid = $this->getStoreId();
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        return view('storeowner.holidayrequest.edit', compact('holidayRequest', 'employees'));
    }

    /**
     * Update the specified holiday request.
     */
    public function update(Request $request, string $requestid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $requestid = base64_decode($requestid);
        $holidayRequest = HolidayRequest::findOrFail($requestid);
        
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $holidayRequest->update([
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'editdatetime' => now(),
            'editip' => $request->ip(),
        ]);
        
        return redirect()->route('storeowner.holidayrequest.index')
            ->with('success', 'Request details updated successfully.');
    }

    /**
     * Remove the specified holiday request.
     */
    public function destroy(string $requestid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $requestid = base64_decode($requestid);
        $holidayRequest = HolidayRequest::findOrFail($requestid);
        $holidayRequest->delete();
        
        return redirect()->route('storeowner.holidayrequest.index')
            ->with('success', 'Request has been deleted successfully');
    }

    /**
     * Change the status of a holiday request.
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'requestid' => 'required|string',
            'status' => 'required|string',
        ]);
        
        $requestid = base64_decode($validated['requestid']);
        $holidayRequest = HolidayRequest::findOrFail($requestid);
        
        $statusParts = explode('-', $validated['status']);
        $status = $statusParts[0];
        
        if ($status === 'Declined') {
            $reason = $request->input('reasonbox');
            if (empty(trim($reason))) {
                return redirect()->route('storeowner.holidayrequest.index')
                    ->with('success', 'Please define reason to declined this request.');
            }
            
            $holidayRequest->update([
                'status' => $status,
                'reason' => $reason,
                'editdatetime' => now(),
                'editip' => $request->ip(),
            ]);
        } else {
            $holidayRequest->update([
                'status' => $status,
                'editdatetime' => now(),
                'editip' => $request->ip(),
            ]);
        }
        
        return redirect()->route('storeowner.holidayrequest.index')
            ->with('success', 'Time Off Request Status changed Successfully.');
    }

    /**
     * Get requests for calendar view (AJAX).
     */
    public function getRequests(Request $request)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return response()->json(['error' => 'Module not installed'], 403);
        }
        
        $storeid = $this->getStoreId();
        
        $holidayRequests = HolidayRequest::with('employee')
            ->where('storeid', $storeid)
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Deactivate');
            })
            ->get();
        
        $colors = [
            'Approved' => 'green',
            'Declined' => 'red',
            'Pending' => 'orange',
        ];
        
        $dataRequests = [];
        foreach ($holidayRequests as $r) {
            if ($r->employee) {
                $dataRequests[] = [
                    'title' => $r->employee->firstname . ' ' . $r->employee->lastname,
                    'color' => $colors[$r->status] ?? 'blue',
                    'end' => $r->to_date->format('Y-m-d'),
                    'start' => $r->from_date->format('Y-m-d'),
                ];
            }
        }
        
        return response()->json(['events' => $dataRequests]);
    }

    /**
     * Calendar view of holiday requests.
     */
    public function calendarView(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        return view('storeowner.holidayrequest.calenderview');
    }

    /**
     * Get requests by employee ID (AJAX modal).
     */
    public function viewRequest(Request $request)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return response('Module not installed', 403);
        }
        
        $employeeid = $request->input('employeeid');
        $storeid = $this->getStoreId();
        
        $requests = HolidayRequest::with('employee')
            ->where('storeid', $storeid)
            ->where('employeeid', $employeeid)
            ->orderBy('requestid', 'DESC')
            ->get();
        
        return response()->view('storeowner.holidayrequest.request_modal', compact('requests'));
    }

    /**
     * Get requests by type (pending, approved, declined).
     */
    public function getRequestByType(string $type): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $query = HolidayRequest::with('employee')
            ->where('storeid', $storeid)
            ->whereHas('employee', function ($q) {
                $q->where('status', '!=', 'Deactivate');
            });
        
        if ($type === 'pending') {
            $query->where('status', 'Pending');
        }
        
        $holidayRequests = $query->orderBy('requestid', 'DESC')->get();
        
        return view('storeowner.holidayrequest.index', compact('holidayRequests'));
    }

    /**
     * Search holiday requests.
     */
    public function search(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $search = $request->input('search', '');
        if (preg_match('/\s/', $search)) {
            $searchParts = explode(' ', $search);
            $search = $searchParts[0];
        }
        
        $storeid = $this->getStoreId();
        
        $holidayRequests = HolidayRequest::with('employee')
            ->where('storeid', $storeid)
            ->where(function ($query) use ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', "%{$search}%")
                      ->orWhere('lastname', 'LIKE', "%{$search}%");
                })
                ->orWhere('from_date', 'LIKE', "%{$search}%")
                ->orWhere('to_date', 'LIKE', "%{$search}%")
                ->orWhere('subject', 'LIKE', "%{$search}%");
            })
            ->orderBy('requestid', 'DESC')
            ->get();
        
        return view('storeowner.holidayrequest.index', compact('holidayRequests', 'search'));
    }
}

