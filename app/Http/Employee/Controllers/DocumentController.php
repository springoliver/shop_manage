<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents for the authenticated employee.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all documents for this employee
        $documents = DB::table('stoma_employee_document as d')
            ->select(
                'd.docid',
                'd.storeid',
                'd.employeeid',
                'd.docname',
                'd.docpath',
                'd.insertdatetime',
                'd.tc_agree',
                'd.signature',
                'd.status',
                'e.firstname',
                'e.lastname'
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'd.employeeid')
            ->where('d.storeid', $employee->storeid)
            ->where('d.employeeid', $employee->employeeid)
            ->where('d.status', '!=', 'Disable')
            ->orderBy('d.docid', 'DESC')
            ->get()
            ->map(function ($document) {
                return [
                    'docid' => $document->docid,
                    'storeid' => $document->storeid,
                    'employeeid' => $document->employeeid,
                    'docname' => $document->docname,
                    'docpath' => $document->docpath,
                    'insertdatetime' => $document->insertdatetime,
                    'tc_agree' => $document->tc_agree,
                    'signature' => $document->signature,
                    'status' => $document->status,
                    'firstname' => $document->firstname ?? '',
                    'lastname' => $document->lastname ?? '',
                ];
            });
        
        return view('employee.document.index', compact('documents'));
    }

    /**
     * Download a document.
     */
    public function download(string $docid): \Symfony\Component\HttpFoundation\StreamedResponse|RedirectResponse
    {
        $docid = base64_decode($docid);
        $employee = Auth::guard('employee')->user();
        
        $document = EmployeeDocument::where('docid', $docid)
            ->where('storeid', $employee->storeid)
            ->where('employeeid', $employee->employeeid)
            ->where('status', '!=', 'Disable')
            ->firstOrFail();
        
        // Try to get file from storage
        if (Storage::disk('public')->exists('documents/' . $document->docpath)) {
            return Storage::disk('public')->download('documents/' . $document->docpath, $document->docname);
        }
        
        // If not in storage, check in public path (CI compatibility)
        $publicPath = public_path('uploads/document/' . $document->docpath);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $document->docname);
        }
        
        return redirect()->route('employee.document.index')
            ->with('error', 'Document file not found.');
    }
}

