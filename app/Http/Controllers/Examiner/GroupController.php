<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index()
    {
        //$this->authorize('viewAny', [Group::class, $organization]);
        $organization = auth()->user()->organizations()->first();

        $groups = $organization->groups()
            ->withCount('members')
            ->latest()
            ->paginate(10);

        return inertia('Examiner/Groups/Index', [
            'organization' => $organization,
            'groups' => $groups,
        ]);
    }

    public function create()
    {
        //$this->authorize('create', [Group::class, $organization]);
        $organization = auth()->user()->organizations()->first();
        return inertia('Examiner/Groups/Create', [
            'organization' => $organization,
        ]);
    }

    public function store(Request $request)
    {
        //$this->authorize('create', [Group::class, $organization]);
        $organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255',
                      Rule::unique('groups')->where('organization_id', $organization->id)],
            'description' => ['nullable', 'string'],
        ]);

        $group = $organization->groups()->create($validated);

        return redirect()->route('Examiner/Groups/Index', [$organization, $group])
            ->with('success', 'Group created successfully');
    }

    public function show(Organization $organization, Group $group)
    {
        //$this->authorize('view', $group);
        $organization = auth()->user()->organizations()->first();
        $group->load(['members', 'quizzes']);

        return inertia('Examiner/Groups/Show', [
            'organization' => $organization,
            'group' => $group,
            'availableUsers' => $organization->members()
                ->whereNotIn('users.id', $group->members->pluck('id'))
                ->get(),
            'availableQuizzes' => $organization->quizzes()
                ->whereNotIn('quizzes.id', $group->quizzes->pluck('id'))
                ->get(),
        ]);
    }

    public function update(Request $request, Organization $organization, Group $group)
    {
        //$this->authorize('update', $group);
        $organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255',
                      Rule::unique('groups')
                          ->ignore($group->id)
                          ->where('organization_id', $organization->id)],
            'description' => ['nullable', 'string'],
        ]);

        $group->update($validated);

        return back()->with('success', 'Group updated successfully');
    }

    public function destroy(Organization $organization, Group $group)
    {
        //$this->authorize('delete', $group);
        $organization = auth()->user()->organizations()->first();
        $group->delete();

        return redirect()->route('examiner.groups.index', $organization)
            ->with('success', 'Group deleted successfully');
    }

    public function addMembers(Request $request, Organization $organization, Group $group)
    {
        $organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id']
        ]);
    
        // For custom pivot table names
        DB::table('group_members')->insert(
            collect($validated['user_ids'])->map(function ($userId) use ($group) {
                return [
                    'group_id' => $group['id'],
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray()
        );
    
        return back()->with('success', 'Members added to group');
    }

    public function removeMember(Organization $organization, Group $group, User $user)
    {
        // 1. Authorization - uncomment when ready
        // $this->authorize('update', $group);
        $organization = auth()->user()->organizations()->first();
        // 2. Verify the group belongs to the organization
        if ($group->organization_id !== $organization->id) {
            abort(403, 'This group does not belong to the specified organization');
        }
    
        // 3. Verify the user is actually a member of this group
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            abort(404, 'User is not a member of this group');
        }
    
        // 4. Verify the user belongs to the organization
        if (!$organization->members()->where('user_id', $user->id)->exists()) {
            abort(403, 'User does not belong to this organization');
        }
    
        // 5. Perform the removal
        $group->members()->detach($user->id);
    
        return back()->with('success', 'Member removed from group');
    }

    public function assignQuizzes(Request $request, Organization $organization, Group $group)
    {
        //$this->authorize('update', $group);
        //$organization = auth()->user()->organizations()->first();
        $validated = $request->validate([
            'quiz_ids' => ['required', 'array'],
            'quiz_ids.*' => ['exists:quizzes,id'],
        ]);
        $organization = auth()->user()->organizations()->first();
        $group->quizzes()->syncWithoutDetaching($validated['quiz_ids']);

        return back()->with('success', 'Quizzes assigned to group');
    }

    public function removeQuiz(Organization $organization, Group $group, Quiz $quiz)
    {
        $organization = auth()->user()->organizations()->first();
        //$this->authorize('update', $group);
        //$organization = auth()->user()->organizations()->first();
        $group->quizzes()->detach($quiz);

        return back()->with('success', 'Quiz removed from group');
    }
}
