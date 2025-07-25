<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExamineeAuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Examinee/Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'unique_code' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('examinee')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('examinee.dashboard'));
        }

        return back()->withErrors([
            'unique_code' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('examinee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('examinee.login');
    }
}
