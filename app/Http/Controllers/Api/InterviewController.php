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
        $interviews = Interview::with('application')->get();
        return response()->json($interviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInterviewRequest $request)
    {
        $validated = $request->validated();

        $interview = Interview::create(array_merge($validated, ['status' => 'scheduled']));

        return response()->json($interview, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $interview = Interview::with('application')->findOrFail($id);
        return response()->json($interview);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInterviewRequest $request, string $id)
    {
        $interview = Interview::findOrFail($id);

        $validated = $request->validated();

        $interview->update($validated);

        return response()->json($interview);
    }

    /**
     * Remove the specified resource from storage. // Or cancel
     */
    public function destroy(string $id)
    {
        $interview = Interview::findOrFail($id);

        // You can either delete it or mark it as cancelled.
        // Let's mark as cancelled to keep the record.
        $interview->update(['status' => 'cancelled']);

        // If hard deletion is preferred instead:
        // $interview->delete();

        return response()->json(['message' => 'Interview cancelled successfully', 'interview' => $interview]);
    }
}
