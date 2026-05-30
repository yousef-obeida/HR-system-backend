<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Requests\Stage\MoveApplicationRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\RejectionMail;
use App\Mail\OfferMail;
use App\Mail\HiredMail;

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
            $newStageId = $validated['stage_id'];

            $stage = Stage::find($newStageId);
            if (!$stage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stage not found.'
                ], 404);
            }

            $status = 'active';
            if (strtolower($stage->name) === 'rejected') {
                $status = 'rejected';
            } elseif (strtolower($stage->name) === 'hired') {
                $status = 'hired';
            }

            $application->update([
                'stage_id' => $newStageId,
                'status' => $status
            ]);

            // Load candidate and job for the mail classes
            $application->load(['candidate', 'job']);

            if ($application->candidate) {
                switch (strtolower($stage->name)) {
                    case 'rejected':
                        Mail::to($application->candidate->email)
                            ->send(new RejectionMail($application));
                        break;
                    case 'offer':
                        Mail::to($application->candidate->email)
                            ->send(new OfferMail($application));
                        break;
                    case 'hired':
                        Mail::to($application->candidate->email)
                            ->send(new HiredMail($application));
                        break;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Application moved successfully and email triggered.',
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
