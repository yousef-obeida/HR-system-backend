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
    public function index()
    {
        $stages = Stage::with('applications.candidate')->get();
        return response()->json($stages);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Move candidate application to a new stage.
     */
    public function moveApplication(MoveApplicationRequest $request, string $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validated();

        $application->update([
            'stage_id' => $validated['stage_id']
        ]);

        return response()->json([
            'message' => 'Application moved successfully',
            'application' => $application
        ]);
    }
}
