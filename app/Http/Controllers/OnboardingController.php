<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function complete(Request $request)
    {
        $user = Auth::user();
        $user->onboarding_completed = true;
        $user->save();

        return response()->json(['message' => 'Onboarding completed']);
    }

    public function restart(Request $request)
    {
        $user = Auth::user();
        $user->onboarding_completed = false;
        $user->save();

        return response()->json(['message' => 'Onboarding restarted']);
    }
}
