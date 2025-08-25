<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $organization = $user->organizations()->first();
        return inertia('Examiner/Settings', [
            'user' => $user,
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'description' => $organization->description,
                'website' => $organization->website,
                'industry' => $organization->industry,
                'office_address' => $organization->office_address,
                'official_phone_number' => $organization->official_phone_number,
                'official_email' => $organization->official_email,
                'official_Whatsapp_contact' => $organization->official_Whatsapp_contact,
                'logo' => $organization->logo,
                'certificate_seal' => $organization->certificate_seal,
            ],
        ]);
    }

    public function updateOrganization(Request $request)
    {
        $organization = auth()->user()->organizations()->first();
        
        // Authorization check
        if (!auth()->user()->hasRole('examiner')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:500',
            'official_phone_number' => 'nullable|string|max:20',
            'official_email' => 'nullable|email',
            'official_Whatsapp_contact' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'certificate_seal' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                Storage::delete($organization->logo);
            }
            $validated['logo'] = $request->file('logo')->store('organization/logos', 'public');
        }

        // Handle certificate seal upload
        if ($request->hasFile('certificate_seal')) {
            // Delete old seal if exists
            if ($organization->certificate_seal) {
                Storage::delete($organization->certificate_seal);
            }
            $validated['certificate_seal'] = $request->file('certificate_seal')->store('organization/seals', 'public');
        }

        $organization->update($validated);

        return back()->with('success', 'Organization details updated successfully');
    }


}
