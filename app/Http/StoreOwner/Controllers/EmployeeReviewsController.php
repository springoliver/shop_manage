<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EmployeeReview;
use App\Models\EmployeeReviewSubject;
use App\Models\StoreEmployee;
use App\Models\UserGroup;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class EmployeeReviewsController extends Controller
{
    protected ModuleService $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Employee Reviews module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Employee Reviews')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of employees with review counts.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all employees with review count
        $employees = DB::table('stoma_employee as e')
            ->select(
                'e.employeeid',
                'e.firstname',
                'e.lastname',
                DB::raw('COUNT(er.insertdatetime) as total_reviews')
            )
            ->leftJoin('stoma_employee_reviews_new as er', function($join) {
                $join->on('er.employeeid', '=', 'e.employeeid');
            })
            ->where('e.status', 'Active')
            ->where('e.storeid', $storeid)
            ->groupBy('e.employeeid', 'e.firstname', 'e.lastname')
            ->get();
        
        return view('storeowner.employeereviews.index', compact('employees'));
    }

    /**
     * Display all reviews.
     */
    public function allReviews(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all reviews grouped by employee and review date
        $reviews = DB::table('stoma_employee_reviews_new as er')
            ->select(
                DB::raw('MIN(er.emp_reviewid) as emp_reviewid'),
                'er.employeeid',
                DB::raw('MIN(er.insertdatetime) as insertdatetime'),
                DB::raw('MIN(e.firstname) as firstname'),
                DB::raw('MIN(e.lastname) as lastname')
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'er.employeeid')
            ->where('er.storeid', $storeid)
            ->groupBy('er.employeeid', DB::raw('DATE(er.insertdatetime)'))
            ->orderBy(DB::raw('MIN(er.insertdatetime)'), 'DESC')
            ->paginate(15);
        
        return view('storeowner.employeereviews.all_reviews', compact('reviews'));
    }

    /**
     * Display due reviews (within 15 days).
     */
    public function dueReviews(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get reviews due within 15 days
        $reviews = DB::table('stoma_employee_reviews_new as er')
            ->select(
                DB::raw('MIN(er.emp_reviewid) as emp_reviewid'),
                'er.employeeid',
                DB::raw('MIN(er.insertdatetime) as insertdatetime'),
                DB::raw('MIN(er.next_review_date) as next_review_date'),
                DB::raw('MIN(e.firstname) as firstname'),
                DB::raw('MIN(e.lastname) as lastname')
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'er.employeeid')
            ->where('er.storeid', $storeid)
            ->where('e.status', 'Active')
            ->whereRaw('er.next_review_date <= DATE_ADD(NOW(), INTERVAL 15 DAY)')
            ->groupBy('er.employeeid', DB::raw('DATE(er.insertdatetime)'))
            ->orderBy(DB::raw('MIN(er.next_review_date)'), 'ASC')
            ->paginate(15);
        
        return view('storeowner.employeereviews.due_reviews', compact('reviews'));
    }

    /**
     * Show reviews for a specific employee.
     */
    public function reviewsByEmployee($employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all reviews for this employee, grouped by review date
        $reviews = DB::table('stoma_employee_reviews_new as er')
            ->select(
                DB::raw('MIN(er.emp_reviewid) as emp_reviewid'),
                'er.employeeid',
                DB::raw('MIN(er.insertdatetime) as insertdatetime'),
                DB::raw('MIN(er.next_review_date) as next_review_date'),
                DB::raw('MIN(e.firstname) as firstname'),
                DB::raw('MIN(e.lastname) as lastname')
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'er.employeeid')
            ->where('er.employeeid', $employeeid)
            ->where('er.storeid', $storeid)
            ->groupBy('er.employeeid', DB::raw('DATE(er.insertdatetime)'))
            ->orderBy(DB::raw('MIN(er.insertdatetime)'), 'DESC')
            ->paginate(15);
        
        return view('storeowner.employeereviews.reviews_by_employee', compact('reviews'));
    }

    /**
     * Show the form for adding a review.
     */
    public function addReview($employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get employee details
        $employee = StoreEmployee::findOrFail($employeeid);
        
        // Get existing reviews for this employee
        $existingReviews = EmployeeReview::where('employeeid', $employeeid)
            ->where('storeid', $storeid)
            ->get()
            ->groupBy(function($review) {
                return $review->insertdatetime->format('Y-m-d H:i:s');
            });
        
        // Get review subjects for this employee's user group
        $reviewSubjects = EmployeeReviewSubject::where('storeid', $storeid)
            ->where('usergroupid', $employee->usergroupid)
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.employeereviews.add_review', compact('employee', 'reviewSubjects', 'existingReviews'));
    }

    /**
     * Store a newly created review.
     */
    public function insertReview(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'employeeid' => 'required|string',
            'review_subjectid' => 'required|array|min:1',
            'review_subjectid.*' => 'required|integer|exists:stoma_employee_review_subjects,review_subjectid',
            'comments' => 'required|array|min:1',
            'comments.*' => 'nullable|string|max:555',
            'next_review_date' => 'required|date',
        ], [
            'review_subjectid.required' => 'Please add at least one review subject. If no subjects are available, please add review subjects first.',
            'review_subjectid.min' => 'Please add at least one review subject. If no subjects are available, please add review subjects first.',
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $employeeid = base64_decode($validated['employeeid']);
        $reviewSubjects = $validated['review_subjectid'];
        $comments = $validated['comments'];
        $nextReviewDate = $validated['next_review_date'];
        $now = now();
        $ip = $request->ip();
        
        $insertData = [];
        foreach ($reviewSubjects as $index => $reviewSubjectId) {
            $insertData[] = [
                'storeid' => $storeid,
                'employeeid' => $employeeid,
                'review_subjectid' => $reviewSubjectId,
                'comments' => $comments[$index] ?? '',
                'next_review_date' => $nextReviewDate,
                'insertdatetime' => $now,
                'insertip' => $ip,
            ];
        }
        
        if (empty($insertData)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No review subjects selected. Please add review subjects first.');
        }
        
        EmployeeReview::insert($insertData);
        
        return redirect()->route('storeowner.employeereviews.index')
            ->with('success', 'Review Added Successfully.');
    }

    /**
     * Show the form for editing a review.
     */
    public function editReview($emp_reviewid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $emp_reviewid = base64_decode($emp_reviewid);
        
        // Get all reviews from the same review session (same insertdatetime)
        $firstReview = EmployeeReview::findOrFail($emp_reviewid);
        
        $reviews = EmployeeReview::where('employeeid', $firstReview->employeeid)
            ->where('storeid', $firstReview->storeid)
            ->where('insertdatetime', $firstReview->insertdatetime)
            ->with('reviewSubject')
            ->get();
        
        if ($reviews->isEmpty()) {
            return redirect()->route('storeowner.employeereviews.index')
                ->with('error', 'Review not found.');
        }
        
        return view('storeowner.employeereviews.edit_review', compact('reviews'));
    }

    /**
     * Update the specified review.
     */
    public function updateReview(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'emp_reviewid' => 'required|array',
            'emp_reviewid.*' => 'required|integer|exists:stoma_employee_reviews_new,emp_reviewid',
            'comments' => 'required|array',
            'comments.*' => 'nullable|string|max:555',
            'next_review_date' => 'required|date',
        ]);
        
        $empReviewIds = $validated['emp_reviewid'];
        $comments = $validated['comments'];
        $nextReviewDate = $validated['next_review_date']; // Single date for all reviews
        
        foreach ($empReviewIds as $index => $empReviewId) {
            $review = EmployeeReview::findOrFail($empReviewId);
            $review->comments = $comments[$index] ?? '';
            $review->next_review_date = $nextReviewDate; // Same date for all
            $review->editdatetime = now();
            $review->editip = $request->ip();
            $review->save();
        }
        
        return redirect()->route('storeowner.employeereviews.index')
            ->with('success', 'Review Updated Successfully.');
    }

    /**
     * Display the specified review.
     */
    public function view($emp_reviewid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $emp_reviewid = base64_decode($emp_reviewid);
        
        // Get all reviews from the same review session
        $firstReview = EmployeeReview::findOrFail($emp_reviewid);
        
        $reviews = EmployeeReview::where('employeeid', $firstReview->employeeid)
            ->where('storeid', $firstReview->storeid)
            ->where('insertdatetime', $firstReview->insertdatetime)
            ->with(['reviewSubject', 'employee'])
            ->get();
        
        return view('storeowner.employeereviews.view', compact('reviews'));
    }

    /**
     * Remove the specified review.
     */
    public function destroy($emp_reviewid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $emp_reviewid = base64_decode($emp_reviewid);
        
        // Delete all reviews from the same review session
        $firstReview = EmployeeReview::findOrFail($emp_reviewid);
        
        EmployeeReview::where('employeeid', $firstReview->employeeid)
            ->where('storeid', $firstReview->storeid)
            ->where('insertdatetime', $firstReview->insertdatetime)
            ->delete();
        
        return redirect()->route('storeowner.employeereviews.index')
            ->with('success', 'Review has been deleted successfully');
    }

    /**
     * Display a listing of review subjects.
     */
    public function reviewSubjects(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $subjects = DB::table('stoma_employee_review_subjects as ers')
            ->select('ers.*', 'u.groupname')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'ers.usergroupid')
            ->where('ers.storeid', $storeid)
            ->orderBy('ers.review_subjectid', 'DESC')
            ->paginate(15);
        
        return view('storeowner.employeereviews.review_subjects', compact('subjects'));
    }

    /**
     * Show the form for creating a new review subject.
     */
    public function addReviewSubject(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $userGroups = UserGroup::where('storeid', $storeid)->get();
        
        // Get all subjects for listing
        $subjects = DB::table('stoma_employee_review_subjects as ers')
            ->select('ers.*', 'u.groupname')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'ers.usergroupid')
            ->where('ers.storeid', $storeid)
            ->orderBy('ers.review_subjectid', 'DESC')
            ->get();
        
        return view('storeowner.employeereviews.add_review_subject', compact('userGroups', 'subjects'));
    }

    /**
     * Store a newly created review subject.
     */
    public function updateReviewSubject(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        if ($request->has('review_subjectid') && !empty($request->review_subjectid)) {
            // Update existing
            $validated = $request->validate([
                'review_subjectid' => 'required|string',
                'usergroupid' => 'required|integer|exists:stoma_usergroup,usergroupid',
                'subject_name' => 'required|string|max:255',
            ]);
            
            $review_subjectid = base64_decode($validated['review_subjectid']);
            $subject = EmployeeReviewSubject::findOrFail($review_subjectid);
            
            $subject->usergroupid = $validated['usergroupid'];
            $subject->subject_name = $validated['subject_name'];
            $subject->editdatetime = now();
            $subject->editip = $request->ip();
            $subject->save();
            
            $message = 'Review Subject Updated Successfully.';
        } else {
            // Create new
            $validated = $request->validate([
                'usergroupid' => 'required|integer|exists:stoma_usergroup,usergroupid',
                'subject_name' => 'required|string|max:255',
            ]);
            
            $user = Auth::guard('storeowner')->user();
            $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
            
            EmployeeReviewSubject::create([
                'storeid' => $storeid,
                'usergroupid' => $validated['usergroupid'],
                'subject_name' => $validated['subject_name'],
                'status' => 'Enable',
                'insertip' => $request->ip(),
            ]);
            
            $message = 'Review Subject Added Successfully.';
        }
        
        return redirect()->route('storeowner.employeereviews.add-review-subject')
            ->with('success', $message);
    }

    /**
     * Show the form for editing a review subject.
     */
    public function editReviewSubject($review_subjectid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $review_subjectid = base64_decode($review_subjectid);
        $subject = EmployeeReviewSubject::with('userGroup')->findOrFail($review_subjectid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $userGroups = UserGroup::where('storeid', $storeid)->get();
        
        return view('storeowner.employeereviews.edit_review_subject', compact('subject', 'userGroups'));
    }

    /**
     * Remove the specified review subject.
     */
    public function destroyReviewSubject($review_subjectid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $review_subjectid = base64_decode($review_subjectid);
        $subject = EmployeeReviewSubject::findOrFail($review_subjectid);
        $subject->delete();
        
        return redirect()->route('storeowner.employeereviews.add-review-subject')
            ->with('success', 'Review Subject has been deleted successfully');
    }

    /**
     * Change review subject status.
     */
    public function changeReviewSubjectStatus(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'review_subjectid' => 'required|string',
            'status' => 'required|in:Enable,Disable',
        ]);
        
        $review_subjectid = base64_decode($validated['review_subjectid']);
        $subject = EmployeeReviewSubject::findOrFail($review_subjectid);
        $subject->status = $validated['status'];
        $subject->save();
        
        return redirect()->route('storeowner.employeereviews.review-subjects')
            ->with('success', 'Status Changed Successfully !');
    }
}

