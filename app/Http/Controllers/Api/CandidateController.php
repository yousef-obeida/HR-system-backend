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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $candidate = \App\Models\Candidate::with(['applications.stage', 'applications.interviews'])->findOrFail($id);
        return response()->json($candidate);
    }

}
