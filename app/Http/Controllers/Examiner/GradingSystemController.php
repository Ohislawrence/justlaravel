<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\GradingSystem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GradingSystemController extends Controller
{
    public function index()
    {
        $organization = auth()->user()->organizations()->first();
        
        // Get default systems and the organization's custom systems
        $defaultSystems = GradingSystem::where('is_default', true)->get();
        $customSystems = $organization->gradingSystems()->get();
        
        // Combine both collections
        $gradingSystems = $defaultSystems->merge($customSystems);
        
        return Inertia::render('Examiner/GradingSystems/Index', [
            'gradingSystems' => $gradingSystems,
            'defaultSystems' => $defaultSystems
        ]);
    }
    
    
    public function create()
    {
        return Inertia::render('Examiner/GradingSystems/Create', [
            'defaultSystems' => GradingSystem::defaultSystems()->get() 
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['4-point', '5-point', 'waec', 'pass-fail', 'custom'])],
            'grade_ranges' => ['required', 'array', 'min:1'],
            'grade_ranges.*.min' => ['required', 'integer', 'min:0', 'max:100'],
            'grade_ranges.*.max' => ['required', 'integer', 'min:0', 'max:100'],
            'grade_ranges.*.grade' => ['required', 'string', 'max:10'],
            'grade_ranges.*.points' => ['nullable', 'numeric', 'min:0'],
            'grade_ranges.*.interpretation' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string']
        ]);
        
        $organization = auth()->user()->organizations()->first();
        
        // Validate grade ranges cover 0-100% without gaps
        $this->validateGradeRanges($validated['grade_ranges']);
        
        $gradingSystem = $organization->gradingSystems()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'grade_ranges' => $validated['grade_ranges'],
            'description' => $validated['description'] ?? null,
            'is_default' => false
        ]);
        
        return redirect()->route('examiner.grading-systems.index')
            ->with('success', 'Grading system created successfully!');
    }
    
    public function edit(GradingSystem $gradingSystem)
    {
        // Ensure the grading system belongs to the user's organization
        $organization = auth()->user()->organizations()->first();
        if ($gradingSystem->organization_id !== $organization->id && !$gradingSystem->is_default) {
            abort(403);
        }
        
        // Prevent editing of default systems (users should create copies instead)
        if ($gradingSystem->is_default) {
            return redirect()->route('examiner.grading-systems.index')
                ->with('error', 'Cannot edit default grading systems. Please create a custom copy instead.');
        }
        
        return Inertia::render('Examiner/GradingSystems/Edit', [
            'gradingSystem' => $gradingSystem,
            'defaultSystems' => GradingSystem::where('is_default', true)->get()
        ]);
    }
    
    
    public function update(Request $request, GradingSystem $gradingSystem)
    {
        // Ensure the grading system belongs to the user's organization
        $organization = auth()->user()->organizations()->first();
        if ($gradingSystem->organization_id !== $organization->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['4-point', '5-point', 'waec', 'pass-fail', 'custom'])],
            'grade_ranges' => ['required', 'array', 'min:1'],
            'grade_ranges.*.min' => ['required', 'integer', 'min:0', 'max:100'],
            'grade_ranges.*.max' => ['required', 'integer', 'min:0', 'max:100'],
            'grade_ranges.*.grade' => ['required', 'string', 'max:10'],
            'grade_ranges.*.points' => ['nullable', 'numeric', 'min:0'],
            'grade_ranges.*.interpretation' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string']
        ]);
        
        // Validate grade ranges cover 0-100% without gaps
        $this->validateGradeRanges($validated['grade_ranges']);
        
        $gradingSystem->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'grade_ranges' => $validated['grade_ranges'],
            'description' => $validated['description'] ?? null,
        ]);
        
        return redirect()->route('examiner.grading-systems.index')
            ->with('success', 'Grading system updated successfully!');
    }
    
    public function destroy(GradingSystem $gradingSystem)
    {
        $organization = auth()->user()->organizations()->first();
        
        // Ensure the grading system belongs to the user's organization
        if ($gradingSystem->organization_id !== $organization->id) {
            abort(403);
        }
        
        // Prevent deletion of default systems
        if ($gradingSystem->is_default) {
            return redirect()->back()
                ->with('error', 'Cannot delete default grading systems.');
        }
        
        // Check if any quizzes are using this grading system
        if ($gradingSystem->quizzes()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete grading system. It is being used by one or more quizzes.');
        }
        
        $gradingSystem->delete();
        
        return redirect()->route('examiner.grading-systems.index')
            ->with('success', 'Grading system deleted successfully!');
    }
    
    public function setDefault(GradingSystem $gradingSystem)
    {
        $organization = auth()->user()->organizations()->first();
        
        // Ensure the grading system belongs to the user's organization
        if ($gradingSystem->organization_id !== $organization->id) {
            abort(403);
        }
        
        // Remove default status from other custom systems using the relationship
        $organization->gradingSystems()
            ->where('id', '!=', $gradingSystem->id)
            ->update(['is_default' => false]);
            
        // Set this system as default
        $gradingSystem->update(['is_default' => true]);
        
        return back()->with('success', 'Grading system set as default!');
    }
    
    private function validateGradeRanges($gradeRanges)
    {
        // Sort by min value
        usort($gradeRanges, function($a, $b) {
            return $a['min'] <=> $b['min'];
        });
        
        // Check if ranges cover 0-100% without gaps
        $expectedMin = 0;
        
        foreach ($gradeRanges as $index => $range) {
            if ($range['min'] != $expectedMin) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'grade_ranges' => "Grade ranges must cover 0-100% without gaps. There's a gap at {$expectedMin}%"
                ]);
            }
            
            $expectedMin = $range['max'] + 1;
            
            // Check if max is less than min
            if ($range['max'] < $range['min']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'grade_ranges' => "Max value cannot be less than min value in range {$range['min']}-{$range['max']}"
                ]);
            }
        }
        
        // Check if the last range goes to 100%
        $lastRange = end($gradeRanges);
        if ($lastRange['max'] != 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'grade_ranges' => "Grade ranges must cover 0-100%. The last range ends at {$lastRange['max']}% instead of 100%"
            ]);
        }
    }
}