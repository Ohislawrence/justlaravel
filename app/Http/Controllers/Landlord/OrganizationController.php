<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;


class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizations = Organization::withCount('members')->latest()->paginate(10);
        
        return Inertia::render('Landlord/Organizations/Index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Landlord/Organizations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'website' => $request->website,
            'industry' => $request->industry,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('landlord.organizations.show', $organization)
            ->with('message', 'Organization created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        return Inertia::render('Landlord/Organizations/Show', [
            'organization' => $organization->loadCount('members'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        return Inertia::render('Landlord/Organizations/Edit', [
            'organization' => $organization,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $organization->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'website' => $request->website,
            'industry' => $request->industry,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('landlord.organizations.show', $organization)
            ->with('message', 'Organization updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();
        
        return redirect()->route('landlord.organizations.index')
            ->with('message', 'Organization deleted successfully');
    }

    public function select()
    {
        $organizations = auth()->user()->organizations;
        
        return inertia('Organizations/Select', [
            'organizations' => $organizations
        ]);
    }

    public function switch(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id'
        ]);
        
        if (auth()->user()->switchOrganization($request->organization_id)) {
            return redirect()->intended('/dashboard')
                ->with('message', 'Organization switched successfully');
        }
        
        return back()->with('error', 'Invalid organization selected');
    }
}
