<?php

namespace App\Actions\Fortify;

use App\Mail\WelcomeEmail;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255', 'unique:organizations,name'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'industry' => ['required', 'string', 'max:255'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make(Str::random(16)),
            'timezone' => $input['timezone'],
            'is_active' => 1,
        ]);
        $user->assignRole(['examiner']);

        $organization  = Organization::create([
            'name'  => $input['organization'],
            'slug' => Str::slug($input['organization']),
            'description' => null,
            'logo' => null,
            'website' => null,
            'industry' => $input['industry'] ?? null,
            'settings' => null,
            'is_active' => 1,
        ]);

        $member = OrganizationMember::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => 'overseer',
            'permissions' => null ,
            'unique_code' => null,
        ]);

        $freePlan = SubscriptionPlan::where('slug', 'free-tier')->first();

        $subscription = $organization->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'billing_cycle' => 'monthly',
            'price' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addYears(10),
            'is_active' => true,
            'is_trial' => false,
        ]);

        $token = Password::createToken($user);
        

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
        
       
        Mail::to($user->email)->queue(new WelcomeEmail($user, $resetUrl));
        
        
        
        return $user;
    }
}
