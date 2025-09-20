<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::with('school')
            ->withCount(['groups', 'students'])
            ->orderBy('name')
            ->get();

        return view('courses.index', compact('courses'));
    }

    public function create(): View
    {
        $schools = School::orderBy('name')->get();

        return view('courses.create', compact('schools'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_id' => ['required', 'exists:schools,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Course::create($validated);

        return redirect()->route('courses.index')->with('status', 'Курс успешно создан.');
    }

    public function show(Course $course): View
    {
        $course->load(['school', 'groups.teacher', 'students']);

        return view('courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $schools = School::orderBy('name')->get();

        return view('courses.edit', compact('course', 'schools'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'school_id' => ['required', 'exists:schools,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $course->update($validated);

        return redirect()->route('courses.index')->with('status', 'Курс обновлен.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('courses.index')->with('status', 'Курс удален.');
    }
}
