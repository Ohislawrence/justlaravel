<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use App\Mail\RegisterWithOrganizationEmail;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Invitation;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

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
            'email' => $invitation->email,
            ]);
    }



    public function create(Request $request)
    {
        $credentials = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255',
            'group'=> 'required',
            'organization' => 'required',
            'timezone' => 'required',
        ]);

        $organization = Organization::where('id',$credentials['organization'])->first();
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // Create new user if doesn't exist
            $user = User::create([
                'name'      => $credentials['name'],
                'email'     => $credentials['email'],
                'password' => Hash::make(Str::random(16)),
                'timezone' => $credentials['timezone'],
                'is_active' => 1,
            ]);

            // Assign role
            $user->assignRole('examinee');

        } else {
            // Check if user is already in this organization
            $existingMember = OrganizationMember::where('user_id', $user->id)
                ->where('organization_id', $organization->id)
                ->exists();
                
            if ($existingMember) {
                return redirect()->back()->with('error', 'You are already a member of an organization.');
            }
        }

        // Create organization membership with designation
        OrganizationMember::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => 'examinee',
            'unique_code' =>  null,
            'designation_id' =>  null,
        ]);

        
        $user->groups()->syncWithoutDetaching($credentials['group']);

        $token = Password::createToken($user);
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        Mail::to($user->email)->queue(new RegisterWithOrganizationEmail($user, $resetUrl,$organization));
        
        return back()->with('success', 'Your account has been created, an email has been sent to you to set your password.');

    }

    public function logout(Request $request)
    {
        Auth::guard('examinee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
