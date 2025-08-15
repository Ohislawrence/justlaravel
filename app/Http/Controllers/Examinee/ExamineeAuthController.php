<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Invitation;

class ExamineeAuthController extends Controller
{
    public function register($organizationSlug, $groupSlug, Request $request)
    {
        $organization = Organization::where('slug', $organizationSlug)->firstOrFail();
        $group = Group::where('slug', $groupSlug)
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        // Validate token if present
        $invitation = null;
        if ($request->has('token')) {
            $invitation = Invitation::where('token', $request->token)
                ->where('group_id', $group->id)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (!$invitation) {
                return redirect()->back()->withErrors([
                    'token' => 'Invalid or expired invitation token'
                ]);
            }
        }
        return Inertia::render('Examinee/Auth/Register', [
            'organization' => $organization,
            'group' => $group,
            'invitation' => $invitation,
            ]);
    }

    public function Create(Request $request)
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
        return redirect()->route('home');
    }
}
