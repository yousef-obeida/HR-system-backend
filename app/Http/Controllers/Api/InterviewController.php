<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;
use App\Http\Requests\Interview\StoreInterviewRequest;
use App\Http\Requests\Interview\UpdateInterviewRequest;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $interviews = Interview::with('application')->get();

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

            $interview = Interview::create(array_merge($validated, ['status' => 'scheduled']));

            return response()->json([
                'success' => true,
                'message' => 'Interview scheduled successfully.',
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

            // You can either delete it or mark it as cancelled.
            // Let's mark as cancelled to keep the record.
            $interview->update(['status' => 'cancelled']);

            // If hard deletion is preferred instead:
            // $interview->delete();

            return response()->json([
                'success' => true,
                'message' => 'Interview cancelled successfully.',
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
