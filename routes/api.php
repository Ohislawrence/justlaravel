<?php

use App\Http\Controllers\Examinee\QuizAttemptController;
use App\Http\Controllers\ProctoringController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


