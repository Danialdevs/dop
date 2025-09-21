@extends('welcome')

@section('title', 'Журнал посещаемости')
@section('page_title', 'Журнал посещаемости')
@section('page_subtitle', 'Укажите группу и период, чтобы отмечать посещаемость')

@section('content')
    @include('partials.flash')

    @php
        $weekdayNames = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
        $formatTime = static fn (?string $time) => $time ? mb_substr($time, 0, 5) : null;
        $gradeSystems = $allowedGradeSystems ?? collect();
        $queryParams = [
            'teacher_id' => $selectedTeacherId ?? null,
            'group_id' => $selectedGroupId ?? null,
            'start_date' => $startDateForView,
            'end_date' => $endDateForView,
        ];
        $attendanceLink = route('journal.index', collect($queryParams + ['view' => 'attendance'])
            ->reject(fn ($value) => $value === null || $value === '')
            ->all());
        $planningLink = route('journal.index', collect($queryParams + ['view' => 'planning'])
            ->reject(fn ($value) => $value === null || $value === '')
            ->all());
    @endphp

    <div class="row g-16">
        <div class="col-12 col-lg-3">
            <div class="bg-white border border-gray-100 rounded-16 h-100">
                <div class="px-24 pt-24 pb-16 border-bottom">
                    <h5 class="mb-0">Разделы журнала</h5>
                </div>
                <div class="px-16 py-16">
                    <nav class="nav flex-column nav-pills gap-8">
                        <a href="{{ $attendanceLink }}" class="nav-link {{ $activeView === 'attendance' ? 'active' : '' }}">
                            Журнал посещаемости
                        </a>
                        <a href="{{ $planningLink }}" class="nav-link {{ $activeView === 'planning' ? 'active' : '' }}">
                            Тематическое планирование
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-9">
            <div class="bg-white border border-gray-100 rounded-16 mb-24">
                <div class="px-24 pt-24 pb-16 border-bottom">
                    <h5 class="mb-0">
                        {{ $activeView === 'planning' ? 'Фильтры тематического планирования' : 'Фильтры журнала' }}
                    </h5>
                </div>
                <div class="px-24 pb-24">
                    <form method="GET" action="{{ route('journal.index') }}" class="row g-16 align-items-end">
                        <input type="hidden" name="view" value="{{ $activeView }}">

                        <div class="col-12 col-lg-4">
                            <label for="teacher_id" class="form-label text-sm text-gray-400">Преподаватель</label>
                            <select id="teacher_id" name="teacher_id" class="form-select" data-auto-submit>
                                <option value="">Все преподаватели</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected($selectedTeacherId === $teacher->id)>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="group_id" class="form-label text-sm text-gray-400">Группа</label>
                            <select id="group_id" name="group_id" class="form-select" data-auto-submit>
                                <option value="">Выберите группу</option>
                                @foreach ($groups as $groupOption)
                                    <option value="{{ $groupOption->id }}" @selected(($selectedGroup && $groupOption->id === $selectedGroup->id) || ($selectedGroupId === $groupOption->id))>
                                        {{ $groupOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="start_date" class="form-label text-sm text-gray-400">С даты</label>
                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                class="form-control"
                                value="{{ $startDateForView }}"
                                {{ $selectedGroup ? '' : 'disabled' }}
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="end_date" class="form-label text-sm text-gray-400">По дату</label>
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                class="form-control"
                                value="{{ $endDateForView }}"
                                {{ $selectedGroup ? '' : 'disabled' }}
                            >
                        </div>

                        <div class="col-12 col-lg-auto">
                            <button type="submit" class="btn btn-main-600 w-100" {{ $selectedGroup ? '' : 'disabled' }}>Показать</button>
                        </div>
                    </form>
                </div>
            </div>

            @if ($activeView === 'attendance')
                @if ($selectedGroup)
                    <div class="bg-white border border-gray-100 rounded-16">
```
            <div class="px-24 pt-24 pb-16 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-16">
                    <div>
                        <h5 class="mb-1">Журнал посещаемости</h5>
                        <div class="text-sm text-gray-300">{{ $selectedGroup->name }}</div>
                    </div>
                    <div class="text-sm text-gray-400 text-end">
                        <div class="fw-semibold text-gray-900">Период</div>
                        <div>
                            {{ $periodStart ? $periodStart->format('d.m.Y') : '—' }}
                            –
                            {{ $periodEnd ? $periodEnd->format('d.m.Y') : '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-24 pb-24">
                @if ($students->isEmpty())
                    <p class="text-gray-200 mb-0">В группе пока нет учеников. Добавьте их, чтобы вести журнал посещаемости.</p>
                @elseif ($lessonsInPeriod->isEmpty())
                    <p class="text-gray-200 mb-0">В выбранном периоде занятий нет. Измените даты или добавьте расписание для группы.</p>
                @else
                    <div class="table-responsive attendance-grid-wrapper">
                        <table class="table table-bordered align-middle attendance-grid mb-20">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-gray-400 fw-medium text-center">№</th>
                                    <th scope="col" class="text-gray-400 fw-medium">ФИО ученика</th>
                                    @foreach ($lessonsInPeriod as $lesson)
                                        @php
                                            $columnKey = 'lesson-' . $lesson->id;
                                            $lessonDate = $lesson->lesson_date?->format('d.m');
                                            $weekdayIndex = $lesson->day_of_week ?? $lesson->lesson_date?->dayOfWeekIso;
                                            $weekdayName = $weekdayIndex ? ($weekdayNames[$weekdayIndex] ?? null) : null;
                                            $weekdayShort = $weekdayName ? mb_strtoupper(mb_substr($weekdayName, 0, 3)) : null;
                                            $startTime = $formatTime($lesson->start_time);
                                            $endTime = $formatTime($lesson->end_time);
                                        @endphp
                                        <th scope="col" class="lesson-column text-center" data-lesson-column="{{ $columnKey }}">
                                            <div class="lesson-header">
                                                <div class="lesson-header-top">
                                                    <span class="lesson-date">{{ $lessonDate ?? '—' }}</span>
                                                    <div class="form-check m-0">
                                                        <input
                                                            class="form-check-input lesson-toggle"
                                                            type="checkbox"
                                                            id="lesson-{{ $lesson->id }}-toggle"
                                                            data-lesson-toggle
                                                            data-lesson-column="{{ $columnKey }}"
                                                            data-update-url="{{ route('lessons.status.update', $lesson) }}"
                                                            @checked($lesson->is_completed)
                                                        >
                                                        <label class="visually-hidden" for="lesson-{{ $lesson->id }}-toggle">Урок проведен</label>
                                                    </div>
                                                </div>
                                                @if ($weekdayShort)
                                                    <div class="lesson-header-meta">{{ $weekdayShort }}</div>
                                                @endif
                                                @if ($startTime)
                                                    <div class="lesson-header-time">{{ $startTime }}–{{ $endTime ?? '—' }}</div>
                                                @endif
                                                <div class="lesson-status-text" data-lesson-status-label>
                                                    {{ $lesson->is_completed ? 'Проведён' : 'Не проведён' }}
                                                </div>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr>
                                        <td class="text-gray-400 text-center">{{ $index + 1 }}</td>
                                        <td class="fw-semibold text-gray-900">
                                            <div class="d-flex align-items-center gap-8">
                                                <span>{{ $student->name }}</span>
                                            </div>
                                        </td>
                                        @foreach ($lessonsInPeriod as $lesson)
                                            @php
                                                $columnKey = 'lesson-' . $lesson->id;
                                            @endphp
                                            <td class="attendance-cell" data-lesson-cell="{{ $columnKey }}">
                                                <div class="attendance-cell-content">
                                                    <button
                                                        type="button"
                                                        class="presence-toggle presence-toggle--absent"
                                                        data-presence-toggle
                                                        data-status="absent"
                                                    >
                                                        Н
                                                    </button>

                                                    <select
                                                        class="form-select form-select-sm grade-select"
                                                        data-grade-select
                                                        data-student-id="{{ $student->id }}"
                                                        data-lesson-id="{{ $lesson->id }}"
                                                        disabled
                                                    >
                                                        <option value="">Оценка</option>
                                                        @foreach ($gradeSystems as $system)
                                                            <optgroup label="{{ $system['label'] }}">
                                                                @foreach ($system['scale'] as $mark)
                                                                    <option value="{{ $mark }}">{{ $mark }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-success d-none" id="attendance-feedback" role="alert">
                        <span data-feedback-text>Оценки сохранены.</span>
                    </div>

                    <div class="d-flex flex-wrap gap-12 justify-content-between align-items-center mt-16">
                        <p class="text-sm text-gray-300 mb-0">
                            Вводите оценки в ячейки и нажмите кнопку, чтобы сохранить. Переключатель «Был/не был» нужен только для визуальной пометки.
                        </p>
                        <button type="button" class="btn btn-main-600" id="attendance-save" disabled>Сохранить оценки</button>
                    </div>
                @else
                    <div class="bg-white border border-dashed border-gray-100 rounded-16 p-32 text-center text-gray-200">
                        Выберите группу и период, чтобы открыть журнал посещаемости.
                    </div>
                @endif
            @else
                @if ($selectedGroup)
                    <div class="bg-white border border-gray-100 rounded-16">
                        <div class="px-24 pt-24 pb-16 border-bottom">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-16">
                                <div>
                                    <h5 class="mb-1">Тематическое планирование</h5>
                                    <div class="text-sm text-gray-300">{{ $selectedGroup->name }}</div>
                                </div>
                                <div class="text-sm text-gray-400 text-end">
                                    <div class="fw-semibold text-gray-900">Период</div>
                                    <div>
                                        {{ $periodStart ? $periodStart->format('d.m.Y') : '—' }}
                                        –
                                        {{ $periodEnd ? $periodEnd->format('d.m.Y') : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-24 pb-24">
                            @if ($lessonsInPeriod->isEmpty())
                                <p class="text-gray-200 mb-0">В выбранных датах занятий нет. Попробуйте изменить период.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th class="text-gray-400 fw-medium">Дата</th>
                                                <th class="text-gray-400 fw-medium">День</th>
                                                <th class="text-gray-400 fw-medium">Время</th>
                                                <th class="text-gray-400 fw-medium">Предмет / тема</th>
                                                <th class="text-gray-400 fw-medium">Аудитория</th>
                                                <th class="text-gray-400 fw-medium text-center">Статус</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lessonsInPeriod as $lesson)
                                                @php
                                                    $weekdayIndex = $lesson->day_of_week ?? $lesson->lesson_date?->dayOfWeekIso;
                                                    $weekdayName = $weekdayIndex ? ($weekdayNames[$weekdayIndex] ?? null) : null;
                                                    $startTime = $formatTime($lesson->start_time);
                                                    $endTime = $formatTime($lesson->end_time);
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold text-gray-900">{{ $lesson->lesson_date?->format('d.m.Y') ?? '—' }}</td>
                                                    <td class="text-gray-400">{{ $weekdayName ?? '—' }}</td>
                                                    <td class="text-gray-400">{{ $startTime ? ($startTime . '–' . ($endTime ?? '—')) : '—' }}</td>
                                                    <td class="text-gray-900">
                                                        <div class="fw-semibold">{{ $lesson->subject ?? 'Без названия' }}</div>
                                                        @if ($lesson->topic)
                                                            <div class="text-sm text-gray-300">{{ $lesson->topic }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-gray-400">{{ $lesson->classroom ?? '—' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge rounded-pill {{ $lesson->is_completed ? 'bg-success-100 text-success-600' : 'bg-gray-100 text-gray-500' }}">
                                                            {{ $lesson->is_completed ? 'Проведён' : 'Запланирован' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white border border-dashed border-gray-100 rounded-16 p-32 text-center text-gray-200">
                        Выберите преподавателя, группу и период, чтобы увидеть тематическое планирование.
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .attendance-grid-wrapper {
            overflow: auto;
            max-height: 520px;
        }

        .attendance-grid {
            min-width: 900px;
        }

        .attendance-grid thead th {
            position: sticky;
            top: 0;
            background-color: #ffffff;
            z-index: 2;
        }

        .attendance-grid th,
        .attendance-grid td {
            vertical-align: middle;
            text-align: center;
        }

        .attendance-grid th:first-child,
        .attendance-grid td:first-child {
            min-width: 60px;
        }

        .attendance-grid thead th:first-child,
        .attendance-grid tbody td:first-child {
            position: sticky;
            left: 0;
            background-color: #ffffff;
            z-index: 3;
        }

        .attendance-grid th:nth-child(2),
        .attendance-grid td:nth-child(2) {
            min-width: 260px;
            text-align: left;
        }

        .attendance-grid thead th:nth-child(2),
        .attendance-grid tbody td:nth-child(2) {
            position: sticky;
            left: 60px;
            background-color: #ffffff;
            z-index: 3;
        }

        .lesson-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .lesson-header-top {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .lesson-date {
            font-weight: 600;
            color: #1f2937;
        }

        .lesson-header-meta {
            font-size: 11px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.04em;
        }

        .lesson-header-time {
            font-size: 11px;
            color: #94a3b8;
        }

        .lesson-status-text {
            font-size: 11px;
            color: #64748b;
        }

        .lesson-column.is-inactive .lesson-date {
            color: #cbd5f5;
        }

        .lesson-column.is-inactive .lesson-header-meta,
        .lesson-column.is-inactive .lesson-header-time,
        .lesson-column.is-inactive .lesson-status-text {
            color: #d1d5db;
        }

        .lesson-column.is-inactive .lesson-toggle {
            opacity: 0.6;
        }

        .attendance-cell {
            padding: 12px 8px;
            background-color: #ffffff;
        }

        .attendance-cell.is-inactive {
            background-color: #f8fafc;
        }

        .attendance-cell-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .attendance-cell.is-inactive .presence-toggle {
            opacity: 0.6;
        }

        .presence-toggle {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 56px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            color: #b91c1c;
            font-weight: 700;
            font-size: 16px;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .presence-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(83, 118, 255, 0.2);
        }

        .presence-toggle--present {
            color: #15803d;
            border-color: #22c55e;
            background-color: #ecfdf3;
        }

        .presence-toggle--absent {
            color: #b91c1c;
            border-color: #ef4444;
            background-color: #fff1f2;
        }

        .grade-select {
            min-width: 120px;
            text-align: center;
            font-weight: 600;
        }

        .grade-select:disabled {
            background-color: #f1f5f9;
            color: #94a3b8;
        }

        .grade-select option {
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const autoSubmitElements = document.querySelectorAll('[data-auto-submit]');
            autoSubmitElements.forEach((element) => {
                element.addEventListener('change', () => {
                    if (element.form) {
                        element.form.submit();
                    }
                });
            });

            const saveButton = document.getElementById('attendance-save');
            const feedback = document.getElementById('attendance-feedback');
            const feedbackText = feedback ? feedback.querySelector('[data-feedback-text]') : null;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? null;

            const markDirty = () => {
                if (saveButton) {
                    saveButton.disabled = false;
                }
                if (feedback) {
                    feedback.classList.add('d-none');
                }
            };

            const setPresenceState = (button, status, options = {}) => {
                const { silent = false } = options;
                if (!button) {
                    return;
                }

                const cell = button.closest('.attendance-cell');
                const gradeSelect = cell ? cell.querySelector('[data-grade-select]') : null;
                const columnActive = cell ? !cell.classList.contains('is-inactive') : true;
                const normalizedStatus = columnActive && status === 'present' ? 'present' : 'absent';
                const isPresent = normalizedStatus === 'present';

                button.dataset.status = normalizedStatus;
                button.classList.remove('presence-toggle--present', 'presence-toggle--absent');
                button.classList.add(isPresent ? 'presence-toggle--present' : 'presence-toggle--absent');
                button.textContent = isPresent ? 'Б' : 'Н';

                if (gradeSelect) {
                    if (isPresent && columnActive) {
                        gradeSelect.disabled = false;
                    } else {
                        gradeSelect.disabled = true;
                        gradeSelect.value = '';
                    }
                }

                if (!silent) {
                    markDirty();
                }
            };

            const lessonToggles = document.querySelectorAll('[data-lesson-toggle]');
            const updateLessonColumnState = (toggle) => {
                const columnKey = toggle.dataset.lessonColumn;
                if (!columnKey) {
                    return;
                }

                const isChecked = toggle.checked;
                const columnCells = document.querySelectorAll(`[data-lesson-cell="${columnKey}"]`);
                const columnHeaders = document.querySelectorAll(`[data-lesson-column="${columnKey}"]`);

                columnCells.forEach((cell) => {
                    cell.classList.toggle('is-inactive', !isChecked);
                    const presenceButton = cell.querySelector('[data-presence-toggle]');
                    setPresenceState(presenceButton, isChecked ? (presenceButton?.dataset.status ?? 'absent') : 'absent', { silent: true });
                });

                columnHeaders.forEach((header) => {
                    header.classList.toggle('is-inactive', !isChecked);
                    const statusLabel = header.querySelector('[data-lesson-status-label]');
                    if (statusLabel) {
                        statusLabel.textContent = isChecked ? 'Проведён' : 'Не проведён';
                    }
                });
            };

            const syncLessonStatus = async (toggle) => {
                const url = toggle.dataset.updateUrl;
                if (!url || !csrfToken) {
                    return;
                }

                toggle.disabled = true;
                try {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            is_completed: toggle.checked ? 1 : 0,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }
                } catch (error) {
                    toggle.checked = !toggle.checked;
                    updateLessonColumnState(toggle);
                    console.error('Не удалось обновить статус урока', error);
                } finally {
                    toggle.disabled = false;
                }
            };

            lessonToggles.forEach((toggle) => {
                updateLessonColumnState(toggle);
                toggle.addEventListener('change', () => {
                    updateLessonColumnState(toggle);
                    syncLessonStatus(toggle);
                });
            });

            const presenceButtons = document.querySelectorAll('[data-presence-toggle]');
            presenceButtons.forEach((button) => {
                const initialStatus = button.dataset.status ?? 'absent';
                setPresenceState(button, initialStatus, { silent: true });
                button.addEventListener('click', () => {
                    const nextStatus = button.dataset.status === 'present' ? 'absent' : 'present';
                    setPresenceState(button, nextStatus);
                });
            });

            const gradeSelects = document.querySelectorAll('[data-grade-select]');
            gradeSelects.forEach((select) => {
                select.addEventListener('change', () => {
                    markDirty();
                });
            });

            const collectGradePayload = () => Array.from(gradeSelects).reduce((accumulator, select) => {
                const grade = select.value;
                if (grade === '') {
                    return accumulator;
                }

                accumulator.push({
                    studentId: select.dataset.studentId,
                    lessonId: select.dataset.lessonId,
                    grade,
                });

                return accumulator;
            }, []);

            if (saveButton) {
                saveButton.addEventListener('click', () => {
                    const payload = collectGradePayload();
                    if (feedback) {
                        const message = payload.length > 0
                            ? `Оценки сохранены (всего ${payload.length}).`
                            : 'Оценки сохранены.';

                        if (feedbackText) {
                            feedbackText.textContent = message;
                        } else {
                            feedback.textContent = message;
                        }

                        feedback.classList.remove('d-none');
                    }

                    saveButton.disabled = true;
                });
            }
        });
    </script>
@endpush
