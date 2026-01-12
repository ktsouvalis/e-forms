<?php

namespace App\Http\Controllers\microapps;

use Carbon\Carbon;
use App\Models\School;
use GuzzleHttp\Client;
use App\Models\Microapp;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\microapps\DailyAbsenceReport;
use App\Http\Requests\microapps\StoreDailyAbsenceReportRequest;

class DailyAbsenceReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->microapp = Microapp::where('url', '/daily_absence_reports')->first();
    }
 /**
     * School view - submit/edit/view their reports
     */
    public function create(Request $request)
    {
        $school = Auth::guard('school')->user();
        
        // Get today's report if exists
        $todayReport = DailyAbsenceReport::where('school_id', $school->id)
            ->forDate(now())
            ->first();
        
        // Get previous reports (last 30 days)
        $previousReports = DailyAbsenceReport::where('school_id', $school->id)
            ->where('report_date', '<', now()->format('Y-m-d'))
            ->orderBy('report_date', 'desc')
            ->limit(30)
            ->get();
        
        // Check if deadline has passed for today
        $deadlinePassed = DailyAbsenceReport::deadlinePassed(now());
       
        return view('microapps/absence_reports.create', compact('todayReport', 'previousReports', 'deadlinePassed'));
    }

    /**
     * Store or update school's daily report
     */
    public function store(StoreDailyAbsenceReportRequest $request)
    {
        $school = Auth::guard('school')->user();
        
        $report = DailyAbsenceReport::updateOrCreate(
            [
                'school_id' => $school->id,
                'report_date' => $request->report_date,
            ],
            [
                'absent_count' => $request->absent_count,
                'comments' => $request->comments,
                'submitted_at' => now(),
            ]
        );
        
        return redirect()->route('daily_absence_reports.create')
            ->with('success', 'Η υποβολή ολοκληρώθηκε επιτυχώς!');
    }

    /**
     * Directorate view - overview of all schools
     */
    public function index(Request $request)
    {
        // Get the date to view (default to today)
        $viewDate = $request->get('date') ? Carbon::parse($request->get('date')) : now();
        
        // Change microapps deadline to today 
        $today  = Carbon::today();
        
        DailyAbsenceReport::setMicroappDeadline($today);

        // Get all schools
        $allSchools = School::where('is_active', 1)->orderBy('name')->get();
        
        // Get reports for the selected date
        $reports = DailyAbsenceReport::forDate($viewDate)
            ->with('school')
            ->get()
            ->keyBy('school_id');
        
        // Calculate statistics
        $totalSchools = $allSchools->count();
        $submittedCount = $reports->count();
        $notSubmittedCount = $totalSchools - $submittedCount;
        $totalAbsent = $reports->sum('absent_count');
        
        // Build data for view
        $schoolsData = $allSchools->map(function ($school) use ($reports) {
            return [
                'id' => $school->id,
                'name' => $school->name,
                'has_submitted' => isset($reports[$school->id]),
                'absent_count' => $reports[$school->id]->absent_count ?? null,
                'comments' => $reports[$school->id]->comments ?? null,
                'submitted_at' => $reports[$school->id]->submitted_at ?? null,
            ];
        });
        $appname = 'daily_absence_reports';
        return view('microapps/absence_reports.index', compact(
            'viewDate',
            'schoolsData',
            'totalSchools',
            'submittedCount',
            'notSubmittedCount',
            'totalAbsent',
            'appname'
        ));
    }


}
