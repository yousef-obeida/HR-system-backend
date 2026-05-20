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

Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum,role:admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('jobs', \App\Http\Controllers\Api\JobController::class);
    Route::apiResource('candidates', CandidateController::class);
    Route::apiResource('interviews', InterviewController::class);
    Route::apiResource('stages', StageController::class);
    Route::patch('applications/{id}/move', [StageController::class, 'moveApplication']);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
});

Route::middleware('auth:sanctum,role:hr')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('jobs', \App\Http\Controllers\Api\JobController::class);
    Route::apiResource('candidates', CandidateController::class);
    Route::apiResource('interviews', InterviewController::class);
    Route::apiResource('stages', StageController::class);
    Route::patch('applications/{id}/move', [StageController::class, 'moveApplication']);
});


Route::middleware('auth:sanctum,role:candidate')->group(function () {
    Route::post('/apply/{job}', [ApplicationController::class, 'store']);
    Route::get('/candidates/{id}', [ApplicationController::class,'index']);
});

