<?php

use App\Http\Controllers\Controller;
use App\Models\Examinee;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamineeController extends Controller
{
    public function index()
    {
        $organizationId = auth()->user()->organization_id;
        
        return Inertia::render('Examiner/Examinees/Index', [
            'examinees' => Examinee::where('organization_id', $organizationId)
                ->with('organization')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Examiner/Examinees/Create', [
            'organizations' => Organization::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:examinees,email',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $examinee = Examinee::create([
            'organization_id' => $request->organization_id,
            'name' => $request->name,
            'email' => $request->email,
            'unique_code' => Examinee::generateUniqueCode(),
            'password' => bcrypt(Examinee::generateUniqueCode()), // Default password is the unique code
        ]);

        return redirect()->route('examiner.examinees.index')
            ->with('success', 'Examinee created successfully. Their login code is: ' . $examinee->unique_code);
    }

    public function edit(Examinee $examinee)
    {
        //$this->authorize('update', $examinee);
        
        return Inertia::render('Examiner/Examinees/Edit', [
            'examinee' => $examinee,
            'organizations' => Organization::all(),
        ]);
    }

    public function update(Request $request, Examinee $examinee)
    {
        //$this->authorize('update', $examinee);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:examinees,email,' . $examinee->id,
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $examinee->update($request->only('name', 'email', 'organization_id'));

        return redirect()->route('examiner.examinees.index')
            ->with('success', 'Examinee updated successfully');
    }

    public function destroy(Examinee $examinee)
    {
        //$this->authorize('delete', $examinee);
        
        $examinee->delete();
        return redirect()->route('examiner.examinees.index')
            ->with('success', 'Examinee deleted successfully');
    }
}
