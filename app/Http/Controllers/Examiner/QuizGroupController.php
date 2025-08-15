<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\QuizGroup;
use App\Models\Organization;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\Response;

class QuizGroupController extends Controller
{
    public function index()
    {
        $organization = auth()->user()->organizations()->first();
        $groupLimit = $organization->getFeatureValue('quiz-group-limit');
        $currentGroupCount = $organization->groups()->count();
        $groups = QuizGroup::with(['children', 'quizzes'])
            ->where('organization_id', $organization->id)
            ->rootGroups()
            ->active()
            ->get();

        return Inertia::render('Examiner/QuizGroups/Index', [
            'organization' => $organization,
            'groups' => $groups,
            'groupLimit' => $groupLimit,
            'currentGroupCount' => $currentGroupCount,
        ]);
    }

    public function create()
    {
        $organization = auth()->user()->organizations()->first();

        $parentGroups = QuizGroup::where('organization_id', $organization->id)
            ->rootGroups()
            ->active()
            ->get();

        return Inertia::render('Examiner/QuizGroups/Create', [
            'organization' => $organization,
            'parentGroups' => $parentGroups,
        ]);
    }

    public function store(Request $request)
    {
        $organization = auth()->user()->organizations()->first();

        $groupLimit = $organization->getFeatureValue('quiz-group-limit');
        $currentGroupCount = $organization->groups()->count();
        if($currentGroupCount < $groupLimit){
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'parent_id' => 'nullable|exists:quiz_groups,id',
                'is_active' => 'boolean',
            ]);

            

            $group = $organization->quizGroups()->create($validated);

            return redirect()->route('examiner.quiz-groups.show', [
                'organization' => $organization->id,
                'quiz_group' => $group->id
            ])->with('success', 'Quiz group created successfully');

        }

        return response('Forbidden access.', Response::HTTP_FORBIDDEN);
    }

    public function show(QuizGroup $quizGroup)
    {
        $quizGroup->load(['children', 'quizzes', 'parent']);
        $organization = auth()->user()->organizations()->first();
        $availableQuizzes = Quiz::where('organization_id', $organization->id)
        ->whereDoesntHave('quizGroups', function($query) use ($quizGroup) {
            $query->where('quiz_group_id', $quizGroup->id);
        })
        ->get();

        return Inertia::render('Examiner/QuizGroups/Show', [
            'organization' => $organization,
            'group' => $quizGroup,
            'quizzes' => $quizGroup->quizzes()->paginate(10),
            'childGroups' => $quizGroup->children()->withCount('quizzes')->get(),
            'availableQuizzes' => $availableQuizzes,
        ]);
    }

    public function edit(QuizGroup $quizGroup)
    {
        $organization = auth()->user()->organizations()->first();
        $parentGroups = QuizGroup::where('organization_id', $organization->id)
            ->rootGroups()
            ->active()
            ->where('id', '!=', $quizGroup->id)
            ->get();

        return Inertia::render('Examiner/QuizGroups/Edit', [
            'organization' => $organization,
            'group' => $quizGroup,
            'parentGroups' => $parentGroups,
        ]);
    }

    public function update(Request $request, QuizGroup $quizGroup)
    {
        $organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:quiz_groups,id',
            'is_active' => 'boolean',
        ]);

        $quizGroup->update($validated);

        return redirect()->route('examiner.quiz-groups.show', [
            'organization' => $organization->id,
            'quiz_group' => $quizGroup->id
        ])->with('success', 'Quiz group updated successfully');
    }

    public function destroy(QuizGroup $quizGroup)
    {
        $organization = auth()->user()->organizations()->first();
        $quizGroup->delete();

        return redirect()->route('examiner.quiz-groups.index', $organization->id)
            ->with('success', 'Quiz group deleted successfully');
    }

    public function addQuiz(Request $request, QuizGroup $quizGroup)
    {
        $organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'order' => 'nullable|integer|min:0',
        ]);
    
        // Check if quiz belongs to the same organization
        $quiz = Quiz::findOrFail($validated['quiz_id']);
        if ($quiz->organization_id !== $organization->id) {
            return back()->with('error', 'Quiz does not belong to this organization');
        }
    
        $quizGroup->quizzes()->syncWithoutDetaching([
            $validated['quiz_id'] => ['order' => $validated['order'] ?? 0]
        ]);
    
        return back()->with('success', 'Quiz added to group successfully');
    }

    public function removeQuiz(QuizGroup $quizGroup, Quiz $quiz)
    {
        $organization = auth()->user()->organizations()->first();
        $quizGroup->quizzes()->detach($quiz->id);

        return back()->with('success', 'Quiz removed from group successfully');
    }
}
