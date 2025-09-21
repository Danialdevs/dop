<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function create(Group $group): View
    {
        $weekdayNames = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];

        $weekStart = Carbon::parse(request('week', now()))->startOfWeek(Carbon::MONDAY);
        $requestedDay = (int) request('day', now()->dayOfWeekIso ?? 1);
        if ($requestedDay < 1 || $requestedDay > 7) {
            $requestedDay = now()->dayOfWeekIso ?: 1;
        }
        $defaultDay = $requestedDay;
        $defaultStart = request('start_time');
        $defaultEnd = request('end_time');
        $defaultTopic = request('subject');

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('lessons.create', [
            'group' => $group,
            'weekdayNames' => $weekdayNames,
            'weekStart' => $weekStart->toDateString(),
            'defaultDay' => $defaultDay,
            'defaultStart' => $defaultStart,
            'defaultEnd' => $defaultEnd,
            'defaultSubject' => $defaultTopic,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'week_start' => ['required', 'date'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'subject' => ['nullable', 'string', 'max:255'],
            'classroom' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'homework' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY);
        $lessonDate = $weekStart->copy()->addDays(((int) $validated['day_of_week']) - 1);

        $fallbackSubject = $validated['subject'] ?? $group->name . ' занятие';

        $lesson = new Lesson([
            'lesson_date' => $lessonDate,
            'day_of_week' => (int) $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'subject' => $fallbackSubject,
            'classroom' => $validated['classroom'] ?? null,
            'week_number' => $lessonDate->isoWeek(),
            'topic' => $validated['topic'] ?? null,
            'objectives' => $validated['objectives'] ?? null,
            'materials' => $validated['materials'] ?? null,
            'homework' => $validated['homework'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_completed' => false,
            'teacher_id' => $validated['teacher_id'] ?? $group->teacher_id,
        ]);

        $group->lessons()->save($lesson);

        return redirect()
            ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $weekStart->toDateString()])
            ->with('status', 'Занятие добавлено.');
    }

    public function edit(Group $group, Lesson $lesson): View
    {
        abort_unless($lesson->group_id === $group->id, 404);

        $weekdayNames = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('lessons.edit', [
            'group' => $group,
            'lesson' => $lesson,
            'weekdayNames' => $weekdayNames,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, Group $group, Lesson $lesson): RedirectResponse
    {
        abort_unless($lesson->group_id === $group->id, 404);

        $validated = $request->validate([
            'week_start' => ['required', 'date'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'subject' => ['required', 'string', 'max:255'],
            'classroom' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'homework' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_completed' => ['nullable', 'boolean'],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY);
        $lessonDate = $weekStart->copy()->addDays(((int) $validated['day_of_week']) - 1);

        $lesson->fill([
            'lesson_date' => $lessonDate,
            'day_of_week' => (int) $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'subject' => $validated['subject'],
            'classroom' => $validated['classroom'] ?? null,
            'week_number' => $lessonDate->isoWeek(),
            'topic' => $validated['topic'] ?? null,
            'objectives' => $validated['objectives'] ?? null,
            'materials' => $validated['materials'] ?? null,
            'homework' => $validated['homework'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_completed' => $request->boolean('is_completed'),
            'teacher_id' => $validated['teacher_id'] ?? $group->teacher_id,
        ]);

        $lesson->save();

        return redirect()
            ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $weekStart->toDateString()])
            ->with('status', 'Занятие обновлено.');
    }

    public function updateStatus(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'is_completed' => ['required', 'boolean'],
        ]);

        $lesson->is_completed = (bool) $validated['is_completed'];
        $lesson->save();

        return response()->json([
            'success' => true,
            'lesson' => [
                'id' => $lesson->id,
                'is_completed' => $lesson->is_completed,
            ],
        ]);
    }

    public function destroy(Group $group, Lesson $lesson): RedirectResponse
    {
        abort_unless($lesson->group_id === $group->id, 404);

        $weekStart = $lesson->lesson_date?->copy()->startOfWeek(Carbon::MONDAY)->toDateString();

        $lesson->delete();

        return redirect()
            ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $weekStart])
            ->with('status', 'Занятие удалено.');
    }

    public function copyWeek(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'source_week_start' => ['required', 'date'],
            'target_week_start' => ['required', 'date'],
            'target_week_end' => ['required', 'date', 'after_or_equal:target_week_start'],
        ]);

        $sourceStart = Carbon::parse($validated['source_week_start'])->startOfWeek(Carbon::MONDAY);
        $targetStart = Carbon::parse($validated['target_week_start'])->startOfWeek(Carbon::MONDAY);
        $targetEnd = Carbon::parse($validated['target_week_end'])->startOfWeek(Carbon::MONDAY);

        $copiedWeeks = 0;
        $copiedLessons = 0;

        DB::transaction(function () use ($group, $sourceStart, $targetStart, $targetEnd, &$copiedWeeks, &$copiedLessons) {
            $currentSourceStart = $sourceStart->copy();
            $currentTargetStart = $targetStart->copy();

            while ($currentTargetStart->lessThanOrEqualTo($targetEnd)) {
                $currentSourceEnd = $currentSourceStart->copy()->endOfWeek(Carbon::SUNDAY);

                $lessons = $group->lessons()
                    ->whereBetween('lesson_date', [$currentSourceStart, $currentSourceEnd])
                    ->orderBy('lesson_date')
                    ->orderBy('start_time')
                    ->get();

                if ($lessons->isEmpty()) {
                    $currentSourceStart->addWeek();
                    $currentTargetStart->addWeek();
                    continue;
                }

                foreach ($lessons as $lesson) {
                    $dayOffset = $lesson->lesson_date->diffInDays($currentSourceStart);
                    $newDate = $currentTargetStart->copy()->addDays($dayOffset);

                    Lesson::updateOrCreate(
                        [
                            'group_id' => $group->id,
                            'lesson_date' => $newDate,
                            'start_time' => $lesson->start_time,
                        ],
                        [
                            'day_of_week' => (int) $newDate->dayOfWeekIso,
                            'end_time' => $lesson->end_time,
                            'subject' => $lesson->subject,
                            'classroom' => $lesson->classroom,
                            'week_number' => $newDate->isoWeek(),
                            'topic' => $lesson->topic,
                            'homework' => $lesson->homework,
                            'notes' => $lesson->notes,
                            'teacher_id' => $lesson->teacher_id,
                            'is_completed' => false,
                        ]
                    );

                    $copiedLessons++;
                }

                $copiedWeeks++;
                $currentSourceStart->addWeek();
                $currentTargetStart->addWeek();
            }
        });

        if ($copiedLessons === 0) {
            return redirect()
                ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $targetStart->toDateString()])
                ->with('status', 'В исходном периоде нет занятий для копирования.');
        }

        $message = "Скопировано {$copiedLessons} занят" . ($copiedLessons === 1 ? 'ие' : ($copiedLessons >= 2 && $copiedLessons <= 4 ? 'ия' : 'ий')) . " за {$copiedWeeks} недель.";

        return redirect()
            ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $targetStart->toDateString()])
            ->with('status', $message);
    }

    public function clearWeek(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'week_start' => ['required', 'date'],
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $deleted = $group->lessons()
            ->whereBetween('lesson_date', [$weekStart, $weekEnd])
            ->delete();

        $message = $deleted ? 'Неделя очищена.' : 'Для выбранной недели нет занятий.';

        return redirect()
            ->route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $weekStart->toDateString()])
            ->with('status', $message);
    }
}
