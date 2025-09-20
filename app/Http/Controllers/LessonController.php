<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validateWithBag('lessonCreation', [
            'lesson_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'subject' => ['required', 'string', 'max:255'],
            'classroom' => ['nullable', 'string', 'max:255'],
            'repeat_weeks' => ['nullable', 'integer', 'min:1', 'max:52'],
        ]);

        $repeatWeeks = (int) ($validated['repeat_weeks'] ?? 1);
        $startDate = Carbon::parse($validated['lesson_date']);
        $firstExistingLessonDate = $group->lessons()->min('lesson_date');
        $baseDate = $firstExistingLessonDate
            ? Carbon::parse($firstExistingLessonDate)
            : $startDate;

        if ($startDate->lt($baseDate)) {
            $baseDate = $startDate->copy();
        }

        $baseWeekStart = $baseDate->copy()->startOfWeek(Carbon::MONDAY);
        $created = 0;
        $skipped = 0;

        for ($week = 0; $week < $repeatWeeks; $week++) {
            $currentDate = $startDate->copy()->addWeeks($week);

            $exists = Lesson::where('group_id', $group->id)
                ->whereDate('lesson_date', $currentDate)
                ->where('start_time', $validated['start_time'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $currentWeekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY);

            Lesson::create([
                'group_id' => $group->id,
                'lesson_date' => $currentDate,
                'day_of_week' => $currentDate->dayOfWeekIso,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'subject' => $validated['subject'],
                'classroom' => $validated['classroom'] ?? null,
                'week_number' => $baseWeekStart->diffInWeeks($currentWeekStart) + 1,
            ]);

            $created++;
        }

        if ($created === 0) {
            $message = 'Новых занятий не создано. Проверьте, возможно, такие занятия уже есть.';
        } else {
            $message = $created === 1
                ? 'Занятие добавлено в расписание.'
                : 'Добавлено занятий: ' . $created . '.';

            if ($skipped > 0) {
                $message .= ' Пропущено: ' . $skipped . ' (уже существует).';
            }
        }

        return redirect()
            ->route('groups.show', $group)
            ->with('status', $message);
    }
}
