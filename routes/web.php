<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Smart HR API']);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'working'
    ]);
});