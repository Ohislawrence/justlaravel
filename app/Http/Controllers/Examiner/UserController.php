<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Mail\NewStudentAddedEmail;
use App\Models\Designation;
use App\Models\Group;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Quiz;
use App\Models\User;
use App\Notifications\NewUserAccountNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Rules\Role;
use Spatie\Permission\Models\Role as ModelsRole;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    
    public function index(Request $request)
    {   
        $organization  = auth()->user()->organizations()->first();
        $organizationId  = $organization->id ;
        //features checker
        $examinerLimit = $organization->getFeatureValue('examiner-limit');
        $examineeLimit = $organization->getFeatureValue('examinee-limit');
        $currentexaminerCount = $organization->members()->role('examiner')->count();
        $currentexamineeCount = $organization->members()->role('examinee')->count();

        $users = User::query()->whereKeyNot(Auth::id())
            ->with(['organizations', 'groups', 'roles','organizationMember.designation']) // Eager load relationships
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
        'users' => $users,
        'organization_id' => $organizationId,
        'examinerLimit' => $examinerLimit,
        'examineeLimit' => $examineeLimit,
        'currentexaminerCount' => $currentexaminerCount,
        'currentexamineeCount' => $currentexamineeCount

        ]);
    }

    public function create()
    {
        $organization = auth()->user()->organizations()->first();
        $examinerLimit = $organization->getFeatureValue('examiner-limit');
        $examineeLimit = $organization->getFeatureValue('examinee-limit');
        $currentexaminerCount = $organization->members()->role('examiner')->count();
        $currentexamineeCount = $organization->members()->role('examinee')->count();
        
        
        //$quizzes = $organization->quizzes()->paginate(10); 
        $groups = $organization->groups()->get(); 
        $designations = $organization->designation ()->get();
        
        
        return Inertia::render('Examiner/User/Create', [
            'examinerLimit' => $examinerLimit,
            'examineeLimit' => $examineeLimit,
            'currentexaminerCount' => $currentexaminerCount,
            'currentexamineeCount' => $currentexamineeCount,
            'groups' => [
            'data' => $groups,
            'total' => $groups->count(),
        ],
        'designations' => $designations,
        ]);
    }

    public function store(Request $request)
    {
        $organization = auth()->user()->organizations()->first();
        $examinerLimit = $organization->getFeatureValue('examiner-limit');
        $examineeLimit = $organization->getFeatureValue('examinee-limit');
        $currentexaminerCount = $organization->members()->role('examiner')->count();
        $currentexamineeCount = $organization->members()->role('examinee')->count();

        $checkExaminer = $examinerLimit < $currentexaminerCount;
        $checkExaminee = $examineeLimit < $currentexamineeCount;

        
        if($request->user_type == 'examinee'){
            if ($checkExaminee == true) {
            return response('Your current plan does not allow you to create more Students/Examinee.', Response::HTTP_FORBIDDEN);
            }
        }
        if($request->user_type == 'examiner'){
            if ($checkExaminer == true) {
            return response('Your current plan does not allow you to create more examiners.', Response::HTTP_FORBIDDEN);
            }
        }
        
        
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255',
            'user_type'  => 'required|in:admin,examiner,examinee',
            'group'     => 'nullable',
            'notify_user' => ['boolean'],
            'unique_code' => 'nullable|string|unique:organization_members,unique_code',
            'designation_id' => 'nullable',
        ]);

        // Get current user's organization
        $organizationId = $organization->id;
        
        // Check if user already exists
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            // Create new user if doesn't exist
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make(Str::random(16)),
                'timezone' => $request->timezone ?? 'Lagos/Africa',
                'is_active' => 1,
            ]);

            // Assign role
            $user->assignRole([$validated['user_type']]);

            
            // Only notify if it's a new user
            if ($validated['notify_user'] ?? false) {
                $user->notify(new NewUserAccountNotification());
            }
        } else {
            // Check if user is already in this organization
            $existingMember = OrganizationMember::where('user_id', $user->id)
                ->where('organization_id', $organizationId)
                ->exists();
                
            if ($existingMember) {
                return redirect()->back()->with('error', 'This user is already a member of your organization.');
            }
        }

        // Create organization membership with designation
        OrganizationMember::create([
            'organization_id' => $organizationId,
            'user_id' => $user->id,
            'role' => $validated['user_type'],
            'unique_code' => $validated['unique_code'] ?? null,
            'designation_id' => $validated['designation_id'] ?? null,
        ]);

        // Attach groups if provided
        if (!empty($validated['group'])) {
            $user->groups()->syncWithoutDetaching($validated['group']);
        }

        

        return redirect()->route('examiner.user.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $organization = auth()->user()->organizations()->first();
        $organizationId = $organization->id;
        // Load necessary relationships
        $user->load([
            'groups',
            'assignedQuizzes',
            'roles',
            'organizationMember.designation' // Load designation through organization member
        ]);
    
        return Inertia::render('Examiner/User/Edit', [
            'user' => $user,
            'groups' => Group::where('organization_id', $organizationId)->get(),
            'quizzes' => Quiz::where('organization_id', $organizationId)->get(),
            'roles' => ModelsRole::all()->pluck('name'),
            'designations' => $organization->designations()->get(), // Corrected relationship
            'initialUserType' => $user->getRoleNames()->first(),
            'initialGroups' => $user->groups()->get(),
            'organizationMember' => $user->organizationMember, // Pass the full member record
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_type' => ['sometimes', 'in:admin,examiner,examinee'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'group_id' => ['nullable', 'exists:groups,id'], // Single group ID
            'quizzes' => ['sometimes', 'array'],
            'quizzes.*' => ['exists:quizzes,id'],
            'designation_id' => ['nullable'],
            'unique_code' => ['nullable'],
        ]);

        // Get the current organization ID
        $organizationId = auth()->user()->organizations()->first()->id;

        DB::transaction(function () use ($validated, $request, $user, $organizationId) {
            // Update basic user info
            $user->update([
                'name' => $validated['name'],
            ]);

            // Update password if provided
            if (!empty($validated['password'])) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            // Update role if provided
            if (!empty($validated['user_type'])) {
                // Remove all current roles first
                $user->syncRoles([$validated['user_type']]);
            }

            // Update organization member details
            $user->organizationMember()->update([
                'designation_id' => $validated['designation_id'] ?? null,
                'unique_code' => $validated['unique_code'] ?? null
            ]);

            // Sync single group if provided
            if (isset($validated['group_id'])) {
                $user->groups()->sync([$validated['group_id']]);
            } else {
                // If no group selected, remove all groups
                $user->groups()->detach();
            }
        });

        return redirect()->route('examiner.user.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Get the current user's organization
        $currentOrganization = OrganizationMember::where('user_id', auth()->id())->firstOrFail();
        
        // Find the organization membership to remove
        $membership = OrganizationMember::where('user_id', $user->id)
            ->where('organization_id', $currentOrganization->organization_id)
            ->firstOrFail();

        // Prevent removing yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot remove yourself from the organization.');
        }

            // Prevent removing last admin
        
    
        // Remove the user from the organization
        $membership->delete();
    
        // Optionally: Remove the user from organization-specific groups
        //$user->groups()->wherePivot('organization_id', $currentOrganization->organization_id)->detach();
    
        // Optionally: Remove organization-specific quiz assignments
        //$user->assignedQuizzes()
         //   ->wherePivot('organization_id', $currentOrganization->organization_id)
         //   ->detach();
    
        return redirect()->back()->with('success', 'User removed from organization successfully.');
    }

    public function designations()
    {
        $organizationId = auth()->user()->organization_id;
        return Designation::where('organization_id', $organizationId)->get();
    }

    public function storeDesignation(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Designation::create([
            'organization_id' => auth()->user()->organizations()->first()->id,
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Designation created successfully');
    }
}
