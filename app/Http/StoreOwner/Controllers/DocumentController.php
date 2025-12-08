<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use App\Models\StoreEmployee;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Employee Documents module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Employee Documents')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of employee documents.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $employeeDocuments = DB::table('stoma_employee_document as d')
            ->select('d.*', 'e.firstname', 'e.lastname', 'e.username', 'e.emailid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'd.employeeid')
            ->where('e.status', '!=', 'Deactivate')
            ->where('d.storeid', $storeid)
            ->orderBy('d.docid', 'DESC')
            ->paginate(15);
        
        return view('storeowner.document.index', compact('employeeDocuments'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->select('firstname', 'lastname', 'employeeid')
            ->get();
        
        return view('storeowner.document.create', compact('employees'));
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'employeeid' => 'required|integer|exists:stoma_employee,employeeid',
            'docname' => 'required|string|max:255',
            'doc' => 'required|file|max:51200', // 50MB in KB
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->hasFile('doc')) {
            $file = $request->file('doc');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . uniqid() . '.' . $extension;
            
            // Store file in storage/app/public/documents
            $filePath = $file->storeAs('documents', $fileName, 'public');
            
            $document = new EmployeeDocument();
            $document->storeid = $storeid;
            $document->employeeid = $validated['employeeid'];
            $document->docname = $validated['docname'];
            $document->docpath = $fileName;
            $document->insertdatetime = now();
            $document->insertip = $request->ip();
            $document->status = 'Enable';
            $document->save();
            
            return redirect()->route('storeowner.document.index')
                ->with('success', 'Document Added Successfully.');
        }
        
        return redirect()->back()
            ->with('error', 'Something went wrong. Please try again.')
            ->withInput();
    }

    /**
     * Get documents by employee ID (AJAX).
     */
    public function getDocuments(Request $request)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'id' => 'required|integer|exists:stoma_employee,employeeid',
        ]);
        
        $employeeDocuments = EmployeeDocument::where('employeeid', $validated['id'])
            ->orderBy('docid', 'DESC')
            ->get();
        
        return view('storeowner.document.modal', [
            'id' => $validated['id'],
            'employee_document' => $employeeDocuments,
        ]);
    }

    /**
     * Remove a document.
     */
    public function destroy($docid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $document = EmployeeDocument::findOrFail($docid);
        
        // Delete file from storage
        if ($document->docpath && Storage::disk('public')->exists('documents/' . $document->docpath)) {
            Storage::disk('public')->delete('documents/' . $document->docpath);
        }
        
        $document->delete();
        
        return redirect()->route('storeowner.document.index')
            ->with('success', 'Record has been deleted successfully');
    }
}

