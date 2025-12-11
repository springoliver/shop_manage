<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HolidayRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HolidayRequestController extends Controller
{
    /**
     * Display a listing of the holiday requests for the authenticated employee.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all holiday requests for this employee
        $holidayRequests = HolidayRequest::with('employee')
            ->where('employeeid', $employee->employeeid)
            ->orderBy('requestid', 'DESC')
            ->get()
            ->map(function ($request) {
                return [
                    'requestid' => $request->requestid,
                    'firstname' => $request->employee->firstname ?? '',
                    'lastname' => $request->employee->lastname ?? '',
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'subject' => $request->subject,
                    'status' => $request->status,
                ];
            });
        
        return view('employee.holidayrequest.index', compact('holidayRequests'));
    }

    /**
     * Show the form for creating a new holiday request.
     */
    public function create(): View
    {
        return view('employee.holidayrequest.create');
    }

    /**
     * Store a newly created holiday request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate date format as dd-mm-yyyy
        $request->validate([
            'from_date' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            'to_date' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $validated = $request->all();
        
        // Validate to_date is after or equal to from_date
        $fromDateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['from_date']);
        $toDateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['to_date']);
        
        if ($toDateObj->lt($fromDateObj)) {
            return redirect()->back()
                ->withErrors(['to_date' => 'The to date must be after or equal to the from date.'])
                ->withInput();
        }
        
        $employee = Auth::guard('employee')->user();
        
        // Parse dates from dd-mm-yyyy format to Y-m-d H:i:s (matching CI's date('Y-m-d H:i:s', strtotime()))
        // Note: CI uses strtotime() which works with dd-mm-yyyy, but Carbon needs explicit format
        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['from_date'])->format('Y-m-d') . ' 00:00:00';
        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['to_date'])->format('Y-m-d') . ' 00:00:00';
        
        HolidayRequest::create([
            'storeid' => $employee->storeid,
            'employeeid' => $employee->employeeid,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Pending',
            'insertdatetime' => now(),
            'insertip' => $request->ip(),
        ]);
        
        return redirect()->route('employee.holidayrequest.index')
            ->with('success', 'Time of request inserted successfully');
    }

    /**
     * Display the specified holiday request.
     */
    public function show(string $requestid): View|RedirectResponse
    {
        $requestid = base64_decode($requestid);
        $employee = Auth::guard('employee')->user();
        
        $holidayRequest = HolidayRequest::with('employee')
            ->where('requestid', $requestid)
            ->where('employeeid', $employee->employeeid)
            ->firstOrFail();
        
        return view('employee.holidayrequest.view', compact('holidayRequest'));
    }

    /**
     * Show the form for editing the specified holiday request.
     */
    public function edit(string $requestid): View|RedirectResponse
    {
        $requestid = base64_decode($requestid);
        $employee = Auth::guard('employee')->user();
        
        $holidayRequest = HolidayRequest::where('requestid', $requestid)
            ->where('employeeid', $employee->employeeid)
            ->where('status', 'Pending') // Only pending requests can be edited
            ->firstOrFail();
        
        return view('employee.holidayrequest.edit', compact('holidayRequest'));
    }

    /**
     * Update the specified holiday request.
     */
    public function update(Request $request, string $requestid): RedirectResponse
    {
        $requestid = base64_decode($requestid);
        $employee = Auth::guard('employee')->user();
        
        $holidayRequest = HolidayRequest::where('requestid', $requestid)
            ->where('employeeid', $employee->employeeid)
            ->where('status', 'Pending') // Only pending requests can be updated
            ->firstOrFail();
        
        // Validate date format as dd-mm-yyyy
        $request->validate([
            'from_date' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            'to_date' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $validated = $request->all();
        
        // Validate to_date is after or equal to from_date
        $fromDateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['from_date']);
        $toDateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['to_date']);
        
        if ($toDateObj->lt($fromDateObj)) {
            return redirect()->back()
                ->withErrors(['to_date' => 'The to date must be after or equal to the from date.'])
                ->withInput();
        }
        
        // Parse dates from dd-mm-yyyy format to Y-m-d H:i:s (matching CI's date('Y-m-d H:i:s', strtotime()))
        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['from_date'])->format('Y-m-d') . ' 00:00:00';
        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['to_date'])->format('Y-m-d') . ' 00:00:00';
        
        $holidayRequest->update([
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'editdatetime' => now(),
            'editip' => $request->ip(),
        ]);
        
        return redirect()->route('employee.holidayrequest.index')
            ->with('success', 'Request details updated successfully.');
    }

    /**
     * Remove the specified holiday request.
     */
    public function destroy(string $requestid): RedirectResponse
    {
        $requestid = base64_decode($requestid);
        $employee = Auth::guard('employee')->user();
        
        $holidayRequest = HolidayRequest::where('requestid', $requestid)
            ->where('employeeid', $employee->employeeid)
            ->where('status', 'Pending') // Only pending requests can be deleted
            ->firstOrFail();
        
        $holidayRequest->delete();
        
        return redirect()->route('employee.holidayrequest.index')
            ->with('success', 'Request has been deleted successfully');
    }

    /**
     * Calendar view of holiday requests.
     */
    public function calendarView(): View
    {
        return view('employee.holidayrequest.calenderview');
    }

    /**
     * Get requests for calendar view (AJAX).
     */
    public function getRequests(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all holiday requests for this employee
        $holidayRequests = HolidayRequest::with('employee')
            ->where('employeeid', $employee->employeeid)
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
}

