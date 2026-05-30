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
        try {
            $totalJobs = Job::where('status', 'open')->count();
            if ($totalJobs === 0) { // Fallback if no open jobs found, just use count
                $totalJobs = Job::count();
            }

            $totalCandidates = Candidate::count();
            $hiredCount = Application::where('status', 'hired')->count();
            $rejectedCount = Application::where('status', 'rejected')->count();
            $interviewCount = Interview::count();

            // Get today's interviews
            $todayInterviews = Interview::with(['application.candidate', 'application.job'])
                ->whereDate('date', now()->toDateString())
                ->orderBy('time')
                ->take(5)
                ->get();

            // Real Data for Hiring Velocity (Last 6 months)
            $hiringVelocity = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = Application::where('status', 'hired')
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->count();
                $hiringVelocity[] = [
                    'month' => $month->format('M'),
                    'hires' => $count
                ];
            }

            // Real Data for AI Insights
            $aiInsights = [];
            $insightId = 1;

            // Insight 1: Stale Jobs
            $staleJob = Job::where('status', 'open')
                ->where('created_at', '<', now()->subDays(30))
                ->orderBy('created_at', 'asc')
                ->first();

            if ($staleJob) {
                $daysOpen = now()->diffInDays($staleJob->created_at);
                $aiInsights[] = [
                    'id' => $insightId++,
                    'type' => 'warning',
                    'message' => "The {$staleJob->title} role has been open for {$daysOpen} days. Consider adjusting the salary or requirements to increase applicant volume.",
                    'action_link' => "/jobs/{$staleJob->id}"
                ];
            }

            // Insight 2: Active Pipeline
            $activeCount = Application::where('status', 'active')->count();
            if ($activeCount > 0) {
                $aiInsights[] = [
                    'id' => $insightId++,
                    'type' => 'success',
                    'message' => "You have {$activeCount} active candidates waiting for review across your open positions.",
                    'action_link' => "/pipelines"
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_jobs' => $totalJobs,
                    'total_candidates' => $totalCandidates,
                    'hired_count' => $hiredCount,
                    'rejected_count' => $rejectedCount,
                    'interview_count' => $interviewCount,
                    'today_interviews' => $todayInterviews,
                    'hiring_velocity' => $hiringVelocity,
                    'ai_insights' => $aiInsights
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
