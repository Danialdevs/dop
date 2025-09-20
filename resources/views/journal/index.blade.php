@extends('welcome')

@section('title', 'Журнал посещаемости')
@section('page_title', 'Журнал посещаемости')
@section('page_subtitle', 'Выберите группу и отметьте присутствие учеников на занятии')

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
    @endphp

    <div class="bg-white border border-gray-100 rounded-16 mb-24">
        <div class="px-24 pt-24 pb-16 border-bottom">
            <h5 class="mb-0">Выбор группы и занятия</h5>
        </div>
        <div class="px-24 pb-24">
            <form method="GET" action="{{ route('journal.index') }}" class="row g-16 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="group_id" class="form-label text-sm text-gray-400">Группа</label>
                    <select id="group_id" name="group_id" class="form-select" data-auto-submit>
                        <option value="">Выберите группу</option>
                        @foreach ($groups as $groupOption)
                            <option value="{{ $groupOption->id }}" @selected($selectedGroup && $groupOption->id === $selectedGroup->id)>
                                {{ $groupOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedGroup && $lessons->isNotEmpty())
                    <div class="col-12 col-lg-4">
                        <label for="lesson_id" class="form-label text-sm text-gray-400">Занятие</label>
                        <select id="lesson_id" name="lesson_id" class="form-select" data-auto-submit>
                            @foreach ($lessons as $lessonOption)
                                @php
                                    $weekdayIndex = $lessonOption->day_of_week ?? $lessonOption->lesson_date?->dayOfWeekIso;
                                    $weekdayName = $weekdayIndex ? ($weekdayNames[$weekdayIndex] ?? null) : null;
                                    $startTime = $formatTime($lessonOption->start_time);
                                    $endTime = $formatTime($lessonOption->end_time);
                                @endphp
                                <option value="{{ $lessonOption->id }}" @selected($selectedLesson && $lessonOption->id === $selectedLesson->id)>
                                    {{ $lessonOption->formatted_date ?: 'Дата не указана' }}
                                    @if ($weekdayName)
                                        • {{ $weekdayName }}
                                    @endif
                                    @if ($startTime)
                                        • {{ $startTime }}–{{ $endTime ?? '—' }}
                                    @endif
                                    @if ($lessonOption->subject)
                                        • {{ $lessonOption->subject }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-12 col-lg-auto">
                    <button type="submit" class="btn btn-main-600 w-100">Показать журнал</button>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedGroup)
        <div class="bg-white border border-gray-100 rounded-16">
            <div class="px-24 pt-24 pb-16 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-16">
                    <div>
                        <h5 class="mb-1">Журнал посещаемости</h5>
                        <div class="text-sm text-gray-300">{{ $selectedGroup->name }}</div>
                    </div>

                    @if ($selectedLesson)
                        @php
                            $lessonWeekdayIndex = $selectedLesson->day_of_week ?? $selectedLesson->lesson_date?->dayOfWeekIso;
                            $lessonWeekday = $lessonWeekdayIndex ? ($weekdayNames[$lessonWeekdayIndex] ?? null) : null;
                            $lessonStart = $formatTime($selectedLesson->start_time);
                            $lessonEnd = $formatTime($selectedLesson->end_time);
                        @endphp
                        <div class="text-sm text-gray-400 text-end">
                            <div class="fw-semibold text-gray-900">{{ $selectedLesson->subject ?? 'Занятие' }}</div>
                            <div>
                                {{ $selectedLesson->formatted_date ?: 'Дата не указана' }}
                                @if ($lessonWeekday)
                                    • {{ $lessonWeekday }}
                                @endif
                                @if ($lessonStart)
                                    • {{ $lessonStart }}–{{ $lessonEnd ?? '—' }}
                                @endif
                                @if ($selectedLesson->classroom)
                                    • {{ $selectedLesson->classroom }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="px-24 pb-24">
                @if ($students->isEmpty())
                    <p class="text-gray-200 mb-0">В группе пока нет учеников. Добавьте их, чтобы вести журнал посещаемости.</p>
                @elseif (! $selectedLesson)
                    <p class="text-gray-200 mb-0">Для выбранной группы ещё нет занятий. Создайте расписание, чтобы отмечать посещаемость.</p>
                @else
                    <div class="d-flex flex-wrap gap-12 align-items-center mb-20">
                        <span class="badge rounded-pill bg-success-100 text-success-600">Присутствуют: <span data-attendance-present>0</span></span>
                        <span class="badge rounded-pill bg-danger-100 text-danger-600">Отсутствуют: <span data-attendance-absent>0</span></span>
                        <span class="badge rounded-pill bg-gray-100 text-gray-500">Без отметки: <span data-attendance-pending>{{ $students->count() }}</span></span>
                    </div>

                    <div id="attendance-feedback" class="alert alert-success d-none" role="alert">
                        Отметки обновлены. Фактическое сохранение появится после подключения учёта посещаемости.
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-20 attendance-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-gray-400 fw-medium">№</th>
                                    <th scope="col" class="text-gray-400 fw-medium">ФИО ученика</th>
                                    <th scope="col" class="text-gray-400 fw-medium text-center">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr class="attendance-row" data-student-id="{{ $student->id }}">
                                        <td class="text-gray-400">{{ $index + 1 }}</td>
                                        <td class="fw-semibold text-gray-900">{{ $student->name }}</td>
                                        <td>
                                            <div class="d-flex flex-column align-items-center gap-8">
                                                <div class="d-flex gap-8">
                                                    <input type="radio" class="btn-check attendance-toggle" name="attendance[{{ $student->id }}]" id="attendance-{{ $student->id }}-present" value="present">
                                                    <label class="btn btn-outline-success btn-sm" for="attendance-{{ $student->id }}-present">Был</label>

                                                    <input type="radio" class="btn-check attendance-toggle" name="attendance[{{ $student->id }}]" id="attendance-{{ $student->id }}-absent" value="absent">
                                                    <label class="btn btn-outline-danger btn-sm" for="attendance-{{ $student->id }}-absent">Не был</label>
                                                </div>
                                                <span class="text-13 text-gray-300" data-attendance-label>Нет отметки</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap gap-12 justify-content-between align-items-center">
                        <p class="text-sm text-gray-300 mb-0">Отметьте посещаемость всех учеников и нажмите кнопку, чтобы зафиксировать результат.</p>
                        <button type="button" class="btn btn-main-600" id="attendance-save" disabled>Сохранить отметки</button>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-100 rounded-16 p-32 text-center text-gray-200">
            Выберите группу, чтобы открыть журнал посещаемости.
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .attendance-row.is-present {
            background-color: #ecfdf3;
        }

        .attendance-row.is-absent {
            background-color: #fff1f2;
        }

        .attendance-table td,
        .attendance-table th {
            vertical-align: middle;
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

            const attendanceInputs = document.querySelectorAll('.attendance-toggle');
            const presentTarget = document.querySelector('[data-attendance-present]');
            const absentTarget = document.querySelector('[data-attendance-absent]');
            const pendingTarget = document.querySelector('[data-attendance-pending]');
            const saveButton = document.getElementById('attendance-save');
            const feedback = document.getElementById('attendance-feedback');

            const updateSummary = () => {
                if (! presentTarget || ! absentTarget || ! pendingTarget) {
                    return;
                }

                const presentCount = document.querySelectorAll('.attendance-toggle[value="present"]:checked').length;
                const absentCount = document.querySelectorAll('.attendance-toggle[value="absent"]:checked').length;
                const totalRows = document.querySelectorAll('.attendance-row').length;
                const pendingCount = Math.max(totalRows - presentCount - absentCount, 0);

                presentTarget.textContent = presentCount;
                absentTarget.textContent = absentCount;
                pendingTarget.textContent = pendingCount;
            };

            attendanceInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    const row = input.closest('.attendance-row');
                    const label = row ? row.querySelector('[data-attendance-label]') : null;

                    if (row) {
                        row.classList.remove('is-present', 'is-absent');
                        if (input.checked) {
                            if (input.value === 'present') {
                                row.classList.add('is-present');
                                if (label) {
                                    label.textContent = 'Отмечен как присутствующий';
                                    label.classList.remove('text-gray-300', 'text-danger-500');
                                    label.classList.add('text-success-500');
                                }
                            } else if (input.value === 'absent') {
                                row.classList.add('is-absent');
                                if (label) {
                                    label.textContent = 'Отмечен как отсутствующий';
                                    label.classList.remove('text-gray-300', 'text-success-500');
                                    label.classList.add('text-danger-500');
                                }
                            }
                        }
                    }

                    if (saveButton) {
                        saveButton.disabled = false;
                    }

                    if (feedback) {
                        feedback.classList.add('d-none');
                    }

                    updateSummary();
                });
            });

            if (saveButton) {
                saveButton.addEventListener('click', () => {
                    saveButton.disabled = true;
                    if (feedback) {
                        feedback.classList.remove('d-none');
                    }
                });
            }

            updateSummary();
        });
    </script>
@endpush
