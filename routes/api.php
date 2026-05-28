<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\StageController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

Route::post('/login', function (Request $request) {

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user
    ]);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
});

Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});

// Shared routes: both admin and hr can access
Route::middleware(['auth:sanctum', 'role:admin,hr'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('jobs', \App\Http\Controllers\Api\JobController::class);
    Route::apiResource('candidates', CandidateController::class)->except(['store', 'update', 'destroy']);
    Route::get('candidates/{candidate}/analysis', [CandidateController::class, 'analysis']);
    Route::get('stages', [StageController::class, 'index']);
    Route::patch('applications/{id}/move', [StageController::class, 'moveApplication']);
});

// HR-only routes
Route::middleware(['auth:sanctum', 'role:hr'])->group(function () {
    Route::apiResource('interviews', InterviewController::class);
});

// Admin-only routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
});


Route::get('/available-jobs', function () {
    $jobs = \App\Models\Job::where('status', 'open')
        ->pluck('title');

    return response()->json([
        'success' => true,
        'data' => $jobs
    ]);
});

Route::get('/apply', [ApplicationController::class, 'create']);
Route::post('/apply/{job}', [ApplicationController::class, 'store']);



