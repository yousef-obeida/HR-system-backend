<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\Interview;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get aggregate stats for the dashboard.
     */
    public function index()
    {
        $totalJobs = Job::count();
        $totalCandidates = Candidate::count();
        $hiredCount = Application::where('status', 'hired')->count();
        $rejectedCount = Application::where('status', 'rejected')->count();
        $interviewCount = Interview::count();

        return response()->json([
            'total_jobs' => $totalJobs,
            'total_candidates' => $totalCandidates,
            'hired_count' => $hiredCount,
            'rejected_count' => $rejectedCount,
            'interview_count' => $interviewCount,
        ]);
    }
}
