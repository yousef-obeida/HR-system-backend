<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $candidates = Candidate::with(['applications.stage', 'applications.interviews'])->get();
        return response()->json($candidates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $candidate = \App\Models\Candidate::with(['applications.stage', 'applications.interviews'])->findOrFail($id);
        return response()->json($candidate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validatedData = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'cv' => 'sometimes|file|mimes:pdf|max:2048'
        ]);

        if ($request->hasFile('cv')) {
            // Delete the old CV if it exists
            if ($candidate->cv_path) {
                Storage::disk('public')->delete($candidate->cv_path);
            }

            // Store the new CV and update the validated data array
            $validatedData['cv_path'] = $request->file('cv')->store('cvs', 'public');
        }

        unset($validatedData['cv']);

        $candidate->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Candidate updated successfully',
            'data' => $candidate
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
