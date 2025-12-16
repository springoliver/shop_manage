<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use App\Models\StoreEmployee;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    use HandlesEmployeeAccess;
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Employee Documents module is installed.
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
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
     * Update document (upload new document from modal).
     */
    public function update(Request $request)
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
        
        $storeid = $this->getStoreId();
        
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
            
            // If AJAX request, return updated modal content
            if ($request->ajax()) {
                $employeeDocuments = EmployeeDocument::where('employeeid', $validated['employeeid'])
                    ->orderBy('docid', 'DESC')
                    ->get();
                
                return view('storeowner.document.modal', [
                    'id' => $validated['employeeid'],
                    'employee_document' => $employeeDocuments,
                ]);
            }
            
            return redirect()->route('storeowner.document.index')
                ->with('success', 'Document Added Successfully.');
        }
        
        if ($request->ajax()) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 400);
        }
        
        return redirect()->back()
            ->with('error', 'Something went wrong. Please try again.')
            ->withInput();
    }

    /**
     * Remove a document.
     */
    public function destroy(Request $request, $docid)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        try {
            $document = EmployeeDocument::findOrFail($docid);
            $employeeid = $document->employeeid;
            
            // Delete file from storage
            if ($document->docpath && Storage::disk('public')->exists('documents/' . $document->docpath)) {
                Storage::disk('public')->delete('documents/' . $document->docpath);
            }
            
            $document->delete();
            
            // If AJAX request (check both ajax() and X-Requested-With header), return updated modal content
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $employeeDocuments = EmployeeDocument::where('employeeid', $employeeid)
                    ->orderBy('docid', 'DESC')
                    ->get();
                
                return view('storeowner.document.modal', [
                    'id' => $employeeid,
                    'employee_document' => $employeeDocuments,
                ]);
            }
            
            return redirect()->route('storeowner.document.index')
                ->with('success', 'Record has been deleted successfully');
        } catch (\Exception $e) {
            // If AJAX request, return error response
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['error' => 'Error deleting document: ' . $e->getMessage()], 500);
            }
            
            return redirect()->route('storeowner.document.index')
                ->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }
}

