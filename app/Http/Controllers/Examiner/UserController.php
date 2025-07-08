<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Quiz;
use App\Models\User;
use App\Notifications\NewUserAccountNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Rules\Role;
use Spatie\Permission\Models\Role as ModelsRole;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $organizationId  = auth()->user()->organizations()->first()->id ;
        $users = User::query()
            ->with(['organizations', 'groups', 'roles']) // Eager load relationships
            ->whereHas('organizations', function($query) use ($organizationId) {
                $query->where('organizations.id', $organizationId);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function($query, $role) {
                $query->where('role', $role);
            })
            ->when($request->group, function($query, $groupId) {
                $query->whereHas('groups', function($q) use ($groupId) {
                    $q->where('groups.id', $groupId);
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('Examiner/User/User', [
        'users' => $users
        ]);
    }

    public function create()
    {
        $organization = auth()->user()->organizations()->first();
        
        // Remove ->get() to return a collection that Inertia can properly handle
        $quizzes = $organization->quizzes()->paginate(10); // or just get() if no pagination
        $groups = $organization->groups()->paginate(10); // or just get() if no pagination
        
        return Inertia::render('Examiner/User/Create', [
            'groups' => $groups,
            'quizzes' => $quizzes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:6',
            'user_type'  => 'required|in:admin,examiner,examinee',
            'groups'     => 'nullable|array',
            'groups.*'   => 'exists:groups,id',
            'quizzes'    => 'nullable|array',
            'quizzes.*'  => 'exists:quizzes,id',
            'notify_user' => ['boolean'],
        ]);
    
        // Create the user
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
        ]);

        $organization = OrganizationMember::where('user_id', auth()->user()->id)->first();

        $user->assignRole([$validated['user_type']]);
        OrganizationMember::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => $validated['user_type'],
            'permissions' => null ,
        ]);

        // Attach groups (assuming many-to-many)
        if (!empty($validated['groups'])) {
            $user->groups()->sync($validated['groups']);
        }
    
        // Attach quizzes (assuming many-to-many)
        if (!empty($validated['quizzes'])) {
            $user->assignedQuizzes()->syncWithoutDetaching([
                $validated['quizzes'] => [
                    'assigned_at' => now(),
                    'due_date' => $validated['due_date'] ?? null,
                    'is_passed' => 0,
                    'attempt_number' =>0,
                ]
            ]);
        }

        if ($validated['notify_user'] ?? false) {
            $user->notify(new NewUserAccountNotification());
        }

    
        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = ModelsRole::all()->pluck('name');
        $groups = Group::all();
        $quizzes = Quiz::all();
        $userType = $user->getRoleNames()->first();
        return Inertia::render('Examiner/User/Edit', ['groups' => $groups, 'quizzes' => $quizzes, 'user' => $user, 'roles' => $roles,'userType'=> $userType]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_type' => ['sometimes'],
            'password' => ['nullable', 'confirmed'],
            'groups' => ['sometimes', 'array'],
            'groups.*' => ['exists:groups,id'],
            'quizzes' => ['sometimes', 'array'],
            'quizzes.*' => ['exists:quizzes,id'],
        ]);

    
        $user->update([
            'name' => $validated['name'],
        ]);

        if (!empty([$request->user_type])) {
            $user->assignRole([$request->user_type]);
        }

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }
    
        if (isset($validated['groups'])) {
            $user->groups()->sync($validated['groups']);
        }
    
        // Attach quizzes (assuming many-to-many)
        if (!empty($validated['quizzes'])) {
            $user->assignedQuizzes()->syncWithoutDetaching([
                $validated['quizzes'] => [
                    'assigned_at' => now(),
                    'due_date' => $validated['due_date'] ?? null,
                    'is_passed' => 0,
                    'attempt_number' =>0,
                ]
            ]);
        }
    
        return redirect()->back()
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }
}
