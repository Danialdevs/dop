<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = Group::with(['course', 'teacher', 'academicYear', 'students'])
            ->latest()
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('groups.create', compact('courses', 'teachers', 'students', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'language' => ['required', 'in:русский,казахский,английский'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'is_active' => ['nullable', 'boolean'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $group = Group::create($validated);

        $group->students()->sync($request->input('student_ids', []));

        return redirect()->route('groups.index')->with('status', 'Группа создана.');
    }

    public function show(Group $group): View
    {
        $group->load(['course', 'teacher', 'students', 'academicYear']);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group): View
    {
        $group->load('students');
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('groups.edit', compact('group', 'courses', 'teachers', 'students', 'academicYears'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'language' => ['required', 'in:русский,казахский,английский'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'is_active' => ['nullable', 'boolean'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $group->update($validated);

        $group->students()->sync($request->input('student_ids', []));

        return redirect()->route('groups.index')->with('status', 'Группа обновлена.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return redirect()->route('groups.index')->with('status', 'Группа удалена.');
    }
}
