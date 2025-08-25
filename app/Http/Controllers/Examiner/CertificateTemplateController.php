<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $organization = auth()->user()->organizations()->firstOrFail();
        $canIssueCertificate = boolval($organization->getFeatureValue('can-issue-certificate'));

        
        return Inertia::render('Examiner/CertificateTemplates/Index', [
            'canIssueCertificate' => $canIssueCertificate,
            'templates' => $organization->certificateTemplates()
                ->latest()
                ->paginate(10),
            'defaultTemplate' => $organization->certificateTemplates()
                ->default()
                ->first()
        ]);
    }

    public function create()
    {
        $organization = auth()->user()->organizations()->first();
        return Inertia::render('Examiner/CertificateTemplates/Create', [
            'organization' => $organization
        ]);
    }

    public function store(Request $request)
    {
        // Get the organization
        $organization = auth()->user()->organizations()->firstOrFail();
        $canIssueCertificate = boolval($organization->getFeatureValue('can-issue-certificate'));
        if (!$canIssueCertificate) {
            return redirect()->back()->with([
                'success' => "Your current plan does not allow the creation of New Certificate Template."
            ]);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_default' => 'boolean'
        ]);

        

        // Handle file upload
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('certificate-templates', 'public');
            $validated['settings'] = [
                'background_image' => $path
            ];
        }

        // Reset any existing default if this is being set as default
        if ($request->is_default) {
            $organization->certificateTemplates()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Create through the relationship
        $organization->certificateTemplates()->create($validated);

        return redirect()
            ->route('examiner.certificate-templates.index')
            ->with('success', 'Template created successfully');
    }

    public function edit(CertificateTemplate $certificateTemplate)
    {
        // Get the current organization (throws 404 if no organization)
        $organization = auth()->user()->organizations()->firstOrFail();
    
        // Verify the template belongs to the organization
        if ($certificateTemplate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized action.');
        }
    
        return Inertia::render('Examiner/CertificateTemplates/Edit', [
            'template' => $certificateTemplate->load('organization'), // Eager load organization
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logo_url' => $organization->logo ? Storage::url($organization->logo) : null,
                'seal_url' => $organization->certificate_seal ? Storage::url($organization->certificate_seal) : null,
            ],
            'currentBackground' => $certificateTemplate->settings['background_image'] 
                ? Storage::url($certificateTemplate->settings['background_image'])
                : null
        ]);
    }

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        // Get the current organization
        $organization = auth()->user()->organizations()->firstOrFail();
    
        // Verify the template belongs to the organization
        if ($certificateTemplate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized action.');
        }
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_default' => 'boolean'
        ]);
    
        // Handle file upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($certificateTemplate->settings['background_image'] ?? false) {
                Storage::disk('public')->delete($certificateTemplate->settings['background_image']);
            }
    
            $path = $request->file('background_image')->store('certificate-templates', 'public');
            $validated['settings'] = [
                'background_image' => $path
            ];
        }
    
        // Reset any existing default if this is being set as default
        if ($request->is_default) {
            // Only reset defaults for the current organization
            $organization->certificateTemplates()
                ->where('is_default', true)
                ->where('id', '!=', $certificateTemplate->id)
                ->update(['is_default' => false]);
        }
    
        $certificateTemplate->update($validated);
    
        return redirect()
            ->route('examiner.certificate-templates.index')
            ->with('success', 'Template updated successfully');
    }

    public function setDefault(CertificateTemplate $certificateTemplate)
    {
        // Get the current organization
        $organization = auth()->user()->organizations()->firstOrFail();

        // Verify the template belongs to the organization
        if ($certificateTemplate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized action.');
        }

        // Reset defaults only for this organization
        $organization->certificateTemplates()
            ->where('is_default', true)
            ->update(['is_default' => false]);

        // Set the selected template as default
        $certificateTemplate->update(['is_default' => true]);

        return redirect()
            ->back()
            ->with('success', 'Default template updated successfully');
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Get the current organization
        $organization = auth()->user()->organizations()->firstOrFail();
    
        // Verify the template belongs to the organization
        if ($certificateTemplate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Prevent deletion of default template
        if ($certificateTemplate->is_default) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete the default template. Please set another template as default first.');
        }
    
        // Use transaction for safety
        DB::transaction(function () use ($certificateTemplate) {
            // Delete associated image if exists
            if ($certificateTemplate->settings['background_image'] ?? false) {
                Storage::disk('public')->delete($certificateTemplate->settings['background_image']);
            }
    
            $certificateTemplate->delete();
        });
    
        return redirect()
            ->route('examiner.certificate-templates.index')
            ->with('success', 'Template deleted successfully');
    }

    public function preview(CertificateTemplate $certificateTemplate)
    {
        // Get the current organization (throws 404 if no organization)
        $organization = auth()->user()->organizations()->firstOrFail();
    
        // Verify the template belongs to the organization
        if ($certificateTemplate->organization_id !== $organization->id) {
            abort(403, 'Unauthorized action.');
        }
    
        // Process the template content with organization assets
        $processedContent = str_replace(
            [
                '{{organization.logo}}',
                '{{organization.seal}}'
            ],
            [
                $organization->logo ? Storage::url($organization->logo) : asset('images/default-logo.png'),
                $organization->certificate_seal ? Storage::url($organization->certificate_seal) : asset('images/default-seal.png')
            ],
            $certificateTemplate->content
        );
    
        return response()->json([
            'html' => $processedContent,
            'settings' => $certificateTemplate->settings,
            'organization' => [
                'name' => $organization->name,
                'logo_url' => $organization->logo ? Storage::url($organization->logo) : null,
                'seal_url' => $organization->certificate_seal ? Storage::url($organization->certificate_seal) : null
            ]
        ]);
    }
}
