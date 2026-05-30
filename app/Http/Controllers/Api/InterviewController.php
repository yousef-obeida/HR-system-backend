<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;
use App\Http\Requests\Interview\StoreInterviewRequest;
use App\Http\Requests\Interview\UpdateInterviewRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\InterviewInvitationMail;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Interview::with('application');
            $query = \App\Filters\InterviewFilter::apply($query, $request);
            $interviews = $query->get();

            return response()->json([
                'success' => true,
                'data' => $interviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve interviews.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInterviewRequest $request)
    {
        try {
            $validated = $request->validated();

            $interview = Interview::create($validated);

            // Send interview invitation email to the candidate
            $interview->load('application.candidate', 'application.job');
            if ($interview->application && $interview->application->candidate) {
                Mail::to($interview->application->candidate->email)
                    ->send(new InterviewInvitationMail($interview->application));
            }

            return response()->json([
                'success' => true,
                'message' => 'Interview scheduled successfully and invitation email sent.',
                'data' => $interview
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule interview.',
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
            $interview = Interview::with('application')->find($id);

            if (!$interview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Interview not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $interview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve interview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInterviewRequest $request, string $id)
    {
        try {
            $interview = Interview::find($id);

            if (!$interview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Interview not found.'
                ], 404);
            }

            $validated = $request->validated();
            $interview->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Interview updated successfully.',
                'data' => $interview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update interview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage. // Or cancel
     */
    public function destroy(string $id)
    {
        try {
            $interview = Interview::find($id);

            if (!$interview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Interview not found.'
                ], 404);
            }

            $interview->delete();

            return response()->json([
                'success' => true,
                'message' => 'Interview deleted successfully.',
                'data' => $interview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel interview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
