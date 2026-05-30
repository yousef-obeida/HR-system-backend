<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CvAnalysisResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidate;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Candidate::with(['applications.stage', 'applications.interviews', 'applications.job', 'analysis']);
            $query = \App\Filters\CandidateFilter::apply($query, $request);
            $candidates = $query->get();

            return response()->json([
                'success' => true,
                'data' => $candidates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve candidates.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $candidate = Candidate::with(['applications.stage', 'applications.interviews', 'applications.job', 'analysis'])->find($id);

            if (!$candidate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Candidate not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $candidate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve candidate.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the AI-generated CV analysis for a candidate.
     */
    public function analysis(Candidate $candidate)
    {
        if (!$candidate->analysis) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis not found'
            ], 404);
        }

        return new CvAnalysisResource($candidate->analysis);
    }
}

