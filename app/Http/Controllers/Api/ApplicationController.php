<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeCVJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Stage;
use Illuminate\Http\Request;
use App\Http\Requests\Application\StoreApplicationRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceivedMail;

class ApplicationController extends Controller
{
    /**
     * Show the application form data with available open jobs.
     * GET /api/apply
     */
    public function create()
    {
        try {
            $jobs = \App\Models\Job::where('status', 'open')
                ->get(['id', 'title']);

            return response()->json([
                'success' => true,
                'data' => [
                    'available_jobs' => $jobs,
                    'fields' => [
                        ['name' => 'job', 'type' => 'select', 'label' => 'Job Position', 'required' => true, 'options' => $jobs],
                        ['name' => 'full_name', 'type' => 'text', 'label' => 'Full Name', 'required' => true],
                        ['name' => 'email', 'type' => 'email', 'label' => 'Email', 'required' => true],
                        ['name' => 'phone_number', 'type' => 'text', 'label' => 'Phone Number', 'required' => true],
                        ['name' => 'cv', 'type' => 'file', 'label' => 'CV (PDF)', 'required' => true, 'accept' => '.pdf'],
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application form.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit the application form.
     * POST /api/apply/{job}
     */
    public function store(StoreApplicationRequest $request, $job)
    {
        try {
            $validatedData = $request->validated();

            // Find the job from the URL parameter and ensure it's open
            $job = \App\Models\Job::where('id', $job)
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or is no longer open.'
                ], 404);
            }

            $path = null;
            if ($request->hasFile('cv')) {
                $path = $request->file('cv')->store('cvs', 'public');
            }

            // Find or create Candidate
            $candidate = Candidate::where('email', $validatedData['email'])->first();
            if ($candidate) {
                // Delete the old CV if it exists
                if ($candidate->cv_path) {
                    Storage::disk('public')->delete($candidate->cv_path);
                }

                $candidate->update([
                    'full_name' => $validatedData['full_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'cv_path' => $path,
                ]);
            } else {
                $candidate = Candidate::create([
                    'email' => $validatedData['email'],
                    'full_name' => $validatedData['full_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'cv_path' => $path,
                ]);
            }

            // Assign 'Applied' Stage
            $stage = Stage::firstOrCreate(
                ['name' => 'Applied'],
                ['order' => 1]
            );

            // Create Application
            $application = Application::create([
                'candidate_id' => $candidate->id,
                'job_post_id' => $job->id,
                'stage_id' => $stage->id,
                'status' => 'active',
            ]);

            // Send Application Received email to candidate
            $application->load(['candidate', 'job']);
            if ($application->candidate) {
                Mail::to($application->candidate->email)
                    ->send(new ApplicationReceivedMail($application));
            }

            // Step 10 — Dispatch AI CV analysis job
            if ($candidate->cv_path) {
                AnalyzeCVJob::dispatch($candidate);
            }

            return response()->json([
                'success' => true,
                'message' => 'Candidate applied successfully',
                'data' => $application->load(['candidate', 'job', 'stage'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
