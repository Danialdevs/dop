<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $groups = Group::orderBy('name')->get(['id', 'name']);

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
            ])->find($selectedGroupId);

            if ($selectedGroup) {
                $lessons = $selectedGroup->lessons;
                $students = $selectedGroup->students;
            }
        }

        $selectedLessonId = $request->integer('lesson_id');
        $selectedLesson = null;

        if ($lessons->isNotEmpty()) {
            if ($selectedLessonId) {
                $selectedLesson = $lessons->firstWhere('id', $selectedLessonId);
            }

            if (! $selectedLesson) {
                $today = Carbon::today();
                $selectedLesson = $lessons->first(function (Lesson $lesson) use ($today) {
                    return $lesson->lesson_date && $lesson->lesson_date->greaterThanOrEqualTo($today);
                }) ?? $lessons->first();

                $selectedLessonId = $selectedLesson?->id;
            }
        }

        return view('journal.index', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'students' => $students,
            'lessons' => $lessons,
            'selectedLesson' => $selectedLesson,
            'selectedLessonId' => $selectedLessonId,
        ]);
    }
}
