<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Requests\Stage\MoveApplicationRequest;

class StageController extends Controller
{
    /**
     * Display a listing of the resource (Kanban view).
     */
    public function index(Request $request)
    {
        try {
            $query = Stage::with('applications.candidate');
            $query = \App\Filters\StageFilter::apply($query, $request);
            $stages = $query->get();

            return response()->json([
                'success' => true,
                'data' => $stages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function moveApplication(MoveApplicationRequest $request, string $id)
    {
        try {
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            $validated = $request->validated();

            $application->update([
                'stage_id' => $validated['stage_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application moved successfully.',
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
