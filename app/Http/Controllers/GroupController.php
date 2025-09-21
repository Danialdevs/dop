<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $selectedAcademicYearId = session('academic_year_id');

        $groups = Group::with(['course', 'teacher', 'academicYear', 'students'])
            ->when($selectedAcademicYearId, fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId))
            ->latest()
            ->get();

        return view('groups.index', [
            'groups' => $groups,
            'selectedAcademicYearId' => $selectedAcademicYearId,
        ]);
    }

    public function create(): View
    {
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $selectedAcademicYearId = session('academic_year_id');

        return view('groups.create', compact('courses', 'teachers', 'students', 'academicYears', 'selectedAcademicYearId'));
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

        if (! $validated['academic_year_id']) {
            $validated['academic_year_id'] = session('academic_year_id');
        }

        $group = Group::create($validated);

        $group->students()->sync($request->input('student_ids', []));

        return redirect()->route('groups.index')->with('status', 'Группа создана.');
    }

    public function show(Group $group): View
    {
        $group->load([
            'course',
            'teacher',
            'students',
            'academicYear',
            'lessons' => fn ($query) => $query
                ->orderBy('week_number')
                ->orderBy('lesson_date')
                ->orderBy('day_of_week')
                ->orderBy('start_time'),
        ]);

        $lessons = $group->lessons;

        $lessonsByWeek = $lessons
            ->groupBy(fn (Lesson $lesson) => $lesson->week_number ?? 1)
            ->sortKeys(SORT_NUMERIC)
            ->map(fn ($weekLessons) => $weekLessons->values());

        $today = Carbon::today();

        $nextLesson = $lessons->first(function (Lesson $lesson) use ($today) {
            return ! $lesson->is_completed
                && $lesson->lesson_date
                && $lesson->lesson_date->greaterThanOrEqualTo($today);
        });

        $weekStart = Carbon::parse(request('week', $today))->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $weekDays = collect(range(0, 6))->map(fn ($offset) => $weekStart->copy()->addDays($offset));

        $currentWeekLessons = $lessons
            ->filter(function (Lesson $lesson) use ($weekStart, $weekEnd) {
                if (! $lesson->lesson_date) {
                    return false;
                }

                return $lesson->lesson_date->greaterThanOrEqualTo($weekStart)
                    && $lesson->lesson_date->lessThanOrEqualTo($weekEnd);
            })
            ->groupBy(fn (Lesson $lesson) => $lesson->lesson_date->toDateString());

        $currentWeekCount = $currentWeekLessons->sum(fn ($dayLessons) => $dayLessons->count());
        $isCurrentWeek = $weekStart->isSameWeek($today, Carbon::MONDAY);

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('groups.show', [
            'group' => $group,
            'lessonsByWeek' => $lessonsByWeek,
            'nextLesson' => $nextLesson,
            'activeTab' => request('tab', 'overview'),
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weekDays' => $weekDays,
            'currentWeekLessons' => $currentWeekLessons,
            'prevWeekStart' => $weekStart->copy()->subWeek(),
            'nextWeekStart' => $weekStart->copy()->addWeek(),
            'currentWeekCount' => $currentWeekCount,
            'isCurrentWeek' => $isCurrentWeek,
            'teachers' => $teachers,
        ]);
    }

    public function edit(Group $group): View
    {
        $group->load('students');
        $courses = Course::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $selectedAcademicYearId = session('academic_year_id');

        return view('groups.edit', compact('group', 'courses', 'teachers', 'students', 'academicYears', 'selectedAcademicYearId'));
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
