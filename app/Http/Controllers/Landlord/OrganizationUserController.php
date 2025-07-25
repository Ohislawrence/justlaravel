<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class OrganizationUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        return Inertia::render('Landlord/Users/Index', [
            'organization' => $organization,
            'users' => $organization->members()
                ->with(['organizationMember.designation', 'groups'])
                ->role('examiner')
                ->paginate(10),
            'designations' => $organization->designations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        return Inertia::render('Landlord/Users/Create', [
            'organization' => $organization,
            'designations' => $organization->designations,
            'groups' => $organization->groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'designation_id' => 'nullable|exists:designations,id',
            'unique_code' => 'nullable|string|max:255',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Assign examiner role
        $user->assignRole('examiner');

        // Add to organization with designation
        $organization->members()->attach($user->id, [
            'role' => 'examiner',
            'designation_id' => $request->designation_id,
            'unique_code' => $request->unique_code,
            'permissions' => json_encode([]),
        ]);

        // Sync groups if provided
        if ($request->filled('groups')) {
            $user->groups()->sync($request->groups);
        }

        return redirect()->route('landlord.organizations.show', $organization)
            ->with('message', 'Examiner created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, User $user)
    {
        // Verify user belongs to organization
        if (!$organization->members->contains($user)) {
            abort(404);
        }

        return Inertia::render('Landlord/Users/Show', [
            'organization' => $organization,
            'user' => $user->load([
                'organizationMember.designation',
                'groups'
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, User $user)
    {
        // Verify user belongs to organization and is an examiner
        if (!$organization->members->contains($user) || !$user->hasRole('examiner')) {
            abort(404);
        }
    
        return Inertia::render('Landlord/Users/Edit', [
            'organization' => $organization,
            'user' => $user->load(['organizationMember', 'groups']),
            'designations' => $organization->designations,
            'groups' => $organization->groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization, User $user)
    {
        // Verify user belongs to organization
        if (!$organization->members->contains($user)) {
            abort(404);
        }
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'designation_id' => 'nullable|exists:designations,id',
            'unique_code' => 'nullable|string|max:255',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'is_active' => 'boolean',
        ]);
    
        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ]);
    
        // Update organization membership
        $user->organizationMember()->update([
            'designation_id' => $request->designation_id,
            'unique_code' => $request->unique_code,
        ]);
    
        // Sync groups
        $user->groups()->sync($request->groups ?? []);
    
        return redirect()->route('landlord.organizations.users.show', [
            'organization' => $organization,
            'user' => $user
        ])->with('message', 'Examiner updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, User $user)
    {
        // Verify user belongs to organization
        if (!$organization->members->contains($user)) {
            abort(404);
        }
    
        // Remove from organization (soft delete the relationship)
        $organization->members()->detach($user->id);
    
        // Optionally: Delete the user completely if they don't belong to any other organizations
        if ($user->organizations()->count() === 0) {
            $user->delete();
        }
    
        return redirect()->route('landlord.organizations.users.index', $organization)
            ->with('message', 'Examiner removed successfully');
    }
}
