<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Stage;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'cv_path' => 'nullable|string', // Consider changing to 'file|mimes:pdf,doc,docx' when implementing file uploads
        ]);

        // Save or find Candidate
        $candidate = Candidate::firstOrCreate(
            ['email' => $validatedData['email']],
            [
                'full_name' => $validatedData['full_name'],
                'phone_number' => $validatedData['phone_number'],
                'cv_path' => $validatedData['cv_path'] ?? 'path/to/default/cv',
            ]
        );

        // Assign 'Applied' Stage
        $stage = Stage::firstOrCreate(
            ['name' => 'Applied'],
            ['order' => 1]
        );

        // Create Application
        $application = Application::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $validatedData['job_post_id'],
            'stage_id' => $stage->id,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Candidate applied successfully',
            'data' => $application->load(['candidate', 'job', 'stage'])
        ], 201);
    }
}
