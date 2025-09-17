<?php

use App\Http\Controllers\Examinee\QuizAttemptController;
use App\Http\Controllers\ProctoringController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/quiz/{quiz}/attempt/{attempt}/save-progress', [QuizAttemptController::class, 'apiSaveProgress'])
    ->name('api.quiz.save-progress')
    ->middleware('auth:sanctum');