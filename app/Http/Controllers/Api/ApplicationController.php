<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Stage;
use Illuminate\Http\Request;
use App\Http\Requests\Application\StoreApplicationRequest;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function store(StoreApplicationRequest $request)
    {
        $validatedData = $request->validated();

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
