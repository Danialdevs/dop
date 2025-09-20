<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', 'student')
            ->with(['school'])
            ->withCount(['studentGroups', 'courses'])
            ->orderBy('name')
            ->get();

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        $schools = School::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();

        return view('students.create', compact('schools', 'groups', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'is_active' => ['nullable', 'boolean'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'school_id' => $validated['school_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $student->studentGroups()->sync($request->input('group_ids', []));
        $student->courses()->sync($request->input('course_ids', []));

        return redirect()->route('students.index')->with('status', 'Ученик создан.');
    }

    public function show(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        $student->load(['school', 'studentGroups.course', 'courses']);

        return view('students.show', compact('student'));
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === 'student', 404);

        $schools = School::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        $student->load(['studentGroups', 'courses']);

        return view('students.edit', compact('student', 'schools', 'groups', 'courses'));
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'is_active' => ['nullable', 'boolean'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        $updates = Arr::only($validated, ['name', 'email', 'phone', 'birth_date', 'school_id']);
        $updates['is_active'] = $request->boolean('is_active');

        if (! empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        $student->update($updates);

        $student->studentGroups()->sync($request->input('group_ids', []));
        $student->courses()->sync($request->input('course_ids', []));

        return redirect()->route('students.index')->with('status', 'Ученик обновлён.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === 'student', 404);

        $student->delete();

        return redirect()->route('students.index')->with('status', 'Ученик удален.');
    }
}
