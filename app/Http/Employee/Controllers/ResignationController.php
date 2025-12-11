<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ResignationController extends Controller
{
    /**
     * Display a listing of the resignations for the authenticated employee.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all resignations for this employee
        $resignations = DB::table('stoma_resignation as r')
            ->select(
                'r.resignationid',
                'r.storeid',
                'r.employeeid',
                'r.from_date',
                'r.subject',
                'r.description',
                'r.insertdatetime',
                'r.insertip',
                'r.status',
                'e.firstname',
                'e.lastname'
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'r.employeeid')
            ->where('r.storeid', $employee->storeid)
            ->where('r.employeeid', $employee->employeeid)
            ->orderBy('r.resignationid', 'DESC')
            ->get()
            ->map(function ($resignation) {
                return [
                    'resignationid' => $resignation->resignationid,
                    'storeid' => $resignation->storeid,
                    'employeeid' => $resignation->employeeid,
                    'from_date' => $resignation->from_date,
                    'subject' => $resignation->subject,
                    'description' => $resignation->description,
                    'insertdatetime' => $resignation->insertdatetime,
                    'insertip' => $resignation->insertip,
                    'status' => $resignation->status,
                    'firstname' => $resignation->firstname ?? '',
                    'lastname' => $resignation->lastname ?? '',
                ];
            });
        
        return view('employee.resignation.index', compact('resignations'));
    }

    /**
     * Show the form for creating a new resignation.
     */
    public function create(): View
    {
        return view('employee.resignation.create');
    }

    /**
     * Store a newly created resignation in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $employee = Auth::guard('employee')->user();
        
        Resignation::create([
            'storeid' => $employee->storeid,
            'employeeid' => $employee->employeeid,
            'from_date' => now(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'insertdatetime' => now(),
            'insertip' => $request->ip(),
            'editdatetime' => null,
            'editip' => '',
            'status' => 'Pending',
        ]);

        return redirect()->route('employee.resignation.index')
            ->with('success', 'Resignation submitted successfully.');
    }

    /**
     * Display the specified resignation.
     */
    public function show(string $resignationid): View|RedirectResponse
    {
        $resignationid = base64_decode($resignationid);
        $employee = Auth::guard('employee')->user();
        
        $resignation = DB::table('stoma_resignation as r')
            ->select(
                'r.resignationid',
                'r.storeid',
                'r.employeeid',
                'r.from_date',
                'r.subject',
                'r.description',
                'r.insertdatetime',
                'r.insertip',
                'r.status',
                'e.firstname',
                'e.lastname'
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'r.employeeid')
            ->where('r.resignationid', $resignationid)
            ->where('r.storeid', $employee->storeid)
            ->where('r.employeeid', $employee->employeeid)
            ->first();
        
        if (!$resignation) {
            abort(404);
        }
        
        // Convert to array for easier access in view
        $resignation = (array) $resignation;
        
        return view('employee.resignation.view', compact('resignation'));
    }
}

