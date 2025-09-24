<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Symfony\Component\HttpFoundation\Response;

class PublicQuizAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $quizId = $request->route('quiz');
        $quiz = Quiz::find($quizId);

        if (!$quiz) {
            abort(404);
        }

        // Allow access if quiz is public
        if ($quiz->is_public) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // For authenticated users, proceed with normal auth checks
        return $next($request);
    }
}