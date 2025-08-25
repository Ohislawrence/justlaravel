<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Mail\GroupInvitationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Invitation;
use App\Jobs\SendGroupInvitation;
use App\Models\Quiz;
use App\Services\UserImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class GroupController extends Controller
{
    public function index()
    {
        //$this->authorize('viewAny', [Group::class, $organization]);
        $organization = auth()->user()->organizations()->first();
        $userGroupLimit = $organization->getFeatureValue('user-group-limit');
        $currentUserGroupCount = $organization->groups()->count();

        $groups = $organization->groups()
            ->withCount('members')
            ->latest()
            ->paginate(10);

        return inertia('Examiner/Groups/Index', [
            'organization' => $organization,
            'groups' => $groups,
            'userGroupLimit' => $userGroupLimit,
            'currentUserGroupCount' => $currentUserGroupCount,
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

        $userGroupLimit = $organization->getFeatureValue('user-group-limit');
        $currentUserGroupCount = $organization->groups()->count();
        if ($currentUserGroupCount >= $userGroupLimit) {
            return redirect()->back()->withErrors([
                'limit' => "You have reached your question limit of {$userGroupLimit}. Upgrade your plan to add more."
            ]);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255',
                      Rule::unique('groups')->where('organization_id', $organization->id)],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $group = $organization->groups()->create($validated);

        return redirect()->route('examiner.groups.index', [$organization, $group])
            ->with('success', 'Group created successfully');
    }

    public function show(Group $group)
    {
        $organization = auth()->user()->organizations()->first();
        
        $group->load(['members', 'quizzes']);
    
        // Get all users in the organization with their current group info
        $organizationUsers = $organization->members()->role('examinee')
            ->with(['groups' => function ($query) use ($organization) {
                $query->where('groups.organization_id', $organization->id)
                      ->whereNull('group_members.left_at');
            }])
            ->get()
            ->map(function ($user) {
                $currentGroup = $user->groups->first();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'current_group_id' => $currentGroup ? $currentGroup->id : null,
                    'current_group_name' => $currentGroup ? $currentGroup->name : null,
                    'has_no_group' => is_null($currentGroup), 
                ];
            });
    
        // Filter out users already in the current group
        $availableUsers = $organizationUsers->filter(function ($user) use ($group) {
            return $user['has_no_group'];
        })->values();
    
        return inertia('Examiner/Groups/Show', [
            'organization' => $organization,
            'group' => $group,
            'availableUsers' => $availableUsers,
            'availableQuizzes' => $organization->quizzes()
                ->whereNotIn('quizzes.id', $group->quizzes->pluck('id'))
                ->get(),
            'allGroups' => $organization->groups()->get(),
            'allGroups' => $organization->groups()->where('id', '!=', $group->id)->get(),
        ]);
    }

    public function moveUsers(Request $request, Organization $organization, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'target_group_id' => ['required', 'exists:groups,id']
        ]);

        $organization = auth()->user()->organizations()->first();

        $targetGroup = Group::findOrFail($validated['target_group_id']);

        DB::transaction(function () use ($validated, $organization, $targetGroup) {
            $users = User::whereIn('id', $validated['user_ids'])->get();

            foreach ($users as $user) {
                // Get all memberships in this org
                $memberships = DB::table('group_members')
                    ->join('groups', 'group_members.group_id', '=', 'groups.id')
                    ->where('group_members.user_id', $user->id)
                    ->where('groups.organization_id', $organization->id)
                    ->select('group_members.*')
                    ->get();

                $mergedHistory = [];

                foreach ($memberships as $membership) {
                    // If target group, skip (we’ll handle below)
                    if ($membership->group_id == $targetGroup->id) {
                        $existingReason = $membership->change_reason
                            ? json_decode($membership->change_reason, true)
                            : [];

                        if (is_array($existingReason)) {
                            $mergedHistory = array_merge($mergedHistory, $existingReason);
                        }

                        continue;
                    }

                    // Build history record with full timeline
                    $historyRecord = [
                        'id'        => $membership->group_id,
                        'joined_at' => $membership->joined_at,
                        'left_at'   => $membership->left_at ?? now()->toDateTimeString(),
                    ];

                    // Merge with any existing history in that row
                    $existingReason = $membership->change_reason
                        ? json_decode($membership->change_reason, true)
                        : [];

                    if (!is_array($existingReason)) {
                        $existingReason = [];
                    }

                    $mergedHistory = array_merge($mergedHistory, $existingReason, [$historyRecord]);

                    // Delete the old row
                    DB::table('group_members')
                        ->where('group_id', $membership->group_id)
                        ->where('user_id', $user->id)
                        ->delete();
                }

                // Check if user already in target group
                $targetMembership = DB::table('group_members')
                    ->where('group_id', $targetGroup->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($targetMembership) {
                    // Merge with existing target history
                    $existingReason = $targetMembership->change_reason
                        ? json_decode($targetMembership->change_reason, true)
                        : [];

                    if (!is_array($existingReason)) {
                        $existingReason = [];
                    }

                    $finalHistory = array_merge($existingReason, $mergedHistory);

                    DB::table('group_members')
                        ->where('group_id', $targetGroup->id)
                        ->where('user_id', $user->id)
                        ->update([
                            'left_at'       => null,
                            'change_reason' => json_encode($finalHistory),
                            'updated_at'    => now(),
                        ]);
                } else {
                    // Create new target record
                    DB::table('group_members')->insert([
                        'group_id'      => $targetGroup->id,
                        'user_id'       => $user->id,
                        'joined_at'     => now(),
                        'left_at'       => null,
                        'change_reason' => json_encode($mergedHistory),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Members moved successfully');
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
        $validated['slug'] = Str::slug($validated['name']);

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
        $validated = $request->validate([
            'quiz_ids' => ['required', 'array'],
            'quiz_ids.*' => ['exists:quizzes,id'],
        ]);
        $organization = auth()->user()->organizations()->first();
        $group->quizzes()->syncWithoutDetaching($validated['quiz_ids']);

        return back()->with('success', 'Quizzes assigned to group');
    }

    public function removeQuiz(Group $group, Quiz $quiz)
    {
        // Verify the user has permission to modify this group
        $organization = auth()->user()->organizations()->first();
        if (!$organization || !$organization->groups->contains($group)) {
            return back()->with('error', 'You do not have permission to modify this group');
        }
    
        // Check if the quiz belongs to the organization
        if ($quiz->organization_id !== $organization->id) {
            return back()->with('error', 'Quiz does not belong to your organization');
        }
    
        // Verify the quiz is actually attached to the group
        if (!$group->quizzes()->where('quiz_id', $quiz->id)->exists()) {
            return back()->with('error', 'Quiz is not assigned to this group');
        }
    
        // Perform the detachment
        try {
            $group->quizzes()->detach($quiz->id);
            
            // Alternative syntax if needed:
            // DB::table('group_quiz')
            //    ->where('group_id', $group->id)
            //    ->where('quiz_id', $quiz->id)
            //    ->delete();
            
            return back()->with('success', 'Quiz successfully removed from group');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove quiz: ' . $e->getMessage());
        }
    }

    public function sendInvite(Request $request, Organization $organization, Group $group)
    {
        $validated = $request->validate([
            'emails' => ['required', 'array'],
            'emails.*' => ['required', 'email']
        ]);

        // Filter out duplicates and existing members
        $existingInvitations = $group->invitations()
            ->whereIn('email', $validated['emails'])
            ->pluck('email');
            
        $existingMembers = $group->members()
            ->whereIn('email', $validated['emails'])
            ->pluck('email');

        $emailsToInvite = collect($validated['emails'])
            ->unique()
            ->reject(fn ($email) => $existingInvitations->contains($email))
            ->reject(fn ($email) => $existingMembers->contains($email));

        if ($emailsToInvite->isEmpty()) {
            return response()->json([
                'message' => 'All provided emails are already invited or members',
                'skipped' => count($validated['emails']) - $emailsToInvite->count()
            ], 422);
        }

        // Create and send invitations
        $sentCount = 0;
        foreach ($emailsToInvite as $email) {
            try {
                $invitation = $group->invitations()->create([
                    'email' => $email,
                    'token' => Str::random(40),
                    'expires_at' => now()->addDays(7)
                ]);
    
                // Dispatch the job instead of sending directly
                SendGroupInvitation::dispatch($invitation);
                $sentCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to create invitation for {$email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => "Invitations sent successfully",
            'sent_count' => $sentCount,
            'skipped_count' => count($validated['emails']) - $sentCount
        ]);
    }


    public function importUsers(Request $request,  Group $group)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
            'timezone' => ['nullable', 'timezone']
        ]);
    
        try {
            $organization = auth()->user()->organizations()->first();
            // Pass both organization AND group to the service
            $importService = new UserImportService($organization, $group);
            
            $success = $importService->importFromCsv($request->file('import_file'), [
                'timezone' => $request->input('timezone', config('app.timezone'))
            ]);
    
            if ($success) {
                $results = $importService->getResults();
                
                return response()->json([
                    'message' => "Import completed successfully. Users added to group.",
                    'results' => $results
                ]);
            }
    
            return response()->json([
                'message' => 'Import failed',
                'errors' => $importService->getErrors()
            ], 422);
    
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Import failed due to server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function importTemplate(Organization $organization)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
        ];

        $template = "name,email\nJohn Doe,john@example.com\nJane Smith,jane@example.com";

        return response($template, 200, $headers);
    }
}
