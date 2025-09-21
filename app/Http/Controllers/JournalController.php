<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Lesson;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $selectedAcademicYearId = session('academic_year_id');
        $activeView = $request->string('view')->lower()->value() === 'planning' ? 'planning' : 'attendance';

        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedTeacherId = $request->integer('teacher_id');

        $groups = Group::with('teacher')
            ->orderBy('name')
            ->when($selectedAcademicYearId, fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId))
            ->when($selectedTeacherId, fn ($query) => $query->where('teacher_id', $selectedTeacherId))
            ->get(['id', 'name', 'teacher_id']);

        $selectedGroupId = $request->integer('group_id');
        $selectedGroup = null;
        $lessons = collect();
        $students = collect();

        if ($selectedGroupId) {
            $selectedGroup = Group::with([
                'students' => fn ($query) => $query->orderBy('name'),
                'lessons' => fn ($query) => $query
                    ->orderBy('week_number')
                    ->orderBy('lesson_date')
                    ->orderBy('day_of_week')
                    ->orderBy('start_time'),
            ])
                ->when($selectedAcademicYearId, fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId))
                ->when($selectedTeacherId, fn ($query) => $query->where('teacher_id', $selectedTeacherId))
                ->find($selectedGroupId);

            if ($selectedGroup) {
                $lessons = $selectedGroup->lessons;
                $students = $selectedGroup->students;
            }
        }

        $periodStart = null;
        $periodEnd = null;
        $lessonsInPeriod = collect();
        $startDateForView = $request->input('start_date');
        $endDateForView = $request->input('end_date');

        if ($selectedGroup) {
            $periodStart = $startDateForView
                ? Carbon::parse($startDateForView)->startOfDay()
                : now()->startOfWeek(Carbon::MONDAY);

            $periodEnd = $endDateForView
                ? Carbon::parse($endDateForView)->endOfDay()
                : $periodStart->copy()->endOfWeek(Carbon::SUNDAY);

            if ($periodEnd->lessThan($periodStart)) {
                $periodEnd = $periodStart->copy()->endOfWeek(Carbon::SUNDAY);
            }

            $lessonsInPeriod = $lessons
                ->filter(function (Lesson $lesson) use ($periodStart, $periodEnd) {
                    if (! $lesson->lesson_date) {
                        return false;
                    }

                    return $lesson->lesson_date->betweenIncluded($periodStart, $periodEnd);
                })
                ->values();
            $startDateForView = $periodStart->toDateString();
            $endDateForView = $periodEnd->toDateString();
        }

        $gradeSystems = collect($this->gradeSystems());
        $school = School::orderBy('id')->first();
        $allowedGradeKeys = collect($school?->allowed_grade_systems ?? $gradeSystems->pluck('key')->toArray());
        $allowedGradeSystems = $gradeSystems
            ->filter(fn ($system) => $allowedGradeKeys->contains($system['key']))
            ->values();

        $gradeOptions = $allowedGradeSystems
            ->flatMap(fn ($system) => $system['scale'])
            ->unique()
            ->values()
            ->all();

        return view('journal.index', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'students' => $students,
            'lessons' => $lessons,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'lessonsInPeriod' => $lessonsInPeriod,
            'startDateForView' => $startDateForView,
            'endDateForView' => $endDateForView,
            'allowedGradeSystems' => $allowedGradeSystems,
            'gradeOptions' => $gradeOptions,
            'teachers' => $teachers,
            'selectedTeacherId' => $selectedTeacherId,
            'activeView' => $activeView,
            'selectedGroupId' => $selectedGroupId,
        ]);
    }

    private function gradeSystems(): array
    {
        return [
            [
                'key' => 'five_point',
                'label' => '5-балльная система',
                'scale' => ['ЗЧ', 'НЗ', '1', '2', '3', '4', '5', '5-', '5+'],
            ],
            [
                'key' => 'american',
                'label' => 'Американская система',
                'scale' => ['ЗЧ', 'НЗ', 'F', 'D', 'C', 'B', 'A', 'A-', 'A+'],
            ],
            [
                'key' => 'ten_point',
                'label' => '10-балльная система',
                'scale' => ['ЗЧ', 'НЗ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
            ],
        ];
    }
}
