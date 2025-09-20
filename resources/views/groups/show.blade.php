@extends('welcome')

@section('title', $group->name)
@section('page_title', $group->name)
@section('page_subtitle', 'Группа')

@section('page_actions')
    <a href="{{ route('groups.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

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
        $highlightLessonId = $nextLesson?->id ?? null;
    @endphp

    <div class="bg-white border border-gray-100 rounded-16 mb-24">
        <div class="px-24 pt-24 pb-16 border-bottom">
            <ul class="nav nav-pills gap-2 flex-wrap" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.edit', $group) }}" class="nav-link text-sm fw-semibold">
                        Изменить информацию о группе
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <span class="nav-link active text-sm fw-semibold disabled" aria-current="page">
                        Расписание группы
                    </span>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('journal.index', ['group_id' => $group->id]) }}" class="nav-link text-sm fw-semibold">
                        Журнал посещаемости
                    </a>
                </li>
            </ul>
        </div>
        <div class="px-24 pb-24">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-gray-400 text-sm">Курс</div>
                    <div class="fw-semibold">{{ $group->course?->name ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-400 text-sm">Преподаватель</div>
                    <div>{{ $group->teacher?->name ?? 'Не назначен' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-400 text-sm">Учебный год</div>
                    <div>{{ $group->academicYear?->name ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-400 text-sm">Язык</div>
                    <div>{{ $group->language }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-gray-400 text-sm">Статус</div>
                    <div>
                        <span class="badge rounded-pill {{ $group->is_active ? 'bg-success-100 text-success-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $group->is_active ? 'Активна' : 'Архив' }}
                        </span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="text-gray-400 text-sm mb-1">Описание</div>
                    <div>{{ $group->description ?: 'Описание не заполнено.' }}</div>
                </div>
            </div>

            @if ($nextLesson)
                @php
                    $nextLessonWeekdayIndex = $nextLesson->day_of_week ?? $nextLesson->lesson_date?->dayOfWeekIso;
                    $nextLessonWeekday = $nextLessonWeekdayIndex ? ($weekdayNames[$nextLessonWeekdayIndex] ?? null) : null;
                    $nextLessonStart = $formatTime($nextLesson->start_time);
                    $nextLessonEnd = $formatTime($nextLesson->end_time);
                @endphp
                <div class="bg-main-50 border border-main-100 rounded-12 p-20 mt-24">
                    <div class="d-flex flex-column flex-lg-row gap-16 justify-content-between">
                        <div>
                            <div class="text-sm text-main-500 fw-semibold text-uppercase mb-2">Ближайшее занятие</div>
                            <div class="fw-semibold text-gray-900">{{ $nextLesson->subject ?? 'Без предмета' }}</div>
                            <div class="text-sm text-gray-400">
                                @if ($nextLessonWeekday)
                                    {{ $nextLessonWeekday }},
                                @endif
                                {{ $nextLesson->formatted_date ?: 'Дата не указана' }}
                                @if ($nextLessonStart)
                                    • {{ $nextLessonStart }}–{{ $nextLessonEnd ?? '—' }}
                                @endif
                                @if ($nextLesson->classroom)
                                    • {{ $nextLesson->classroom }}
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-400">
                            @if ($nextLesson->topic)
                                <div><span class="text-gray-300 fw-medium">Тема:</span> {{ $nextLesson->topic }}</div>
                            @endif
                            @if ($nextLesson->homework)
                                <div class="mt-2"><span class="text-gray-300 fw-medium">ДЗ:</span> {{ $nextLesson->homework }}</div>
                            @endif
                            @unless ($nextLesson->topic || $nextLesson->homework)
                                <div class="text-gray-300">Детали урока не заполнены.</div>
                            @endunless
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-16 mb-24">
        <div class="px-24 pt-24 pb-16 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-12">
                <h5 class="mb-0">Расписание занятий</h5>
                <span class="text-sm text-gray-300">Запланируйте занятия на нужное количество недель</span>
            </div>
        </div>
        <div class="px-24 pb-24">
            @if ($lessonsByWeek->isEmpty())
                <p class="text-gray-200 mb-0">Расписание ещё не создано. Добавьте занятия, чтобы увидеть их здесь.</p>
            @else
                @foreach ($lessonsByWeek as $weekNumber => $weekLessons)
                    <div class="border border-gray-100 rounded-12 p-20 mb-24">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-12 mb-20">
                            <div class="fw-semibold text-gray-900">Неделя {{ $weekNumber }}</div>
                            <div class="text-sm text-gray-300">Всего занятий: {{ $weekLessons->count() }}</div>
                        </div>

                        <ul class="lesson-list list-unstyled mb-0">
                            @foreach ($weekLessons as $lesson)
                                @php
                                    $weekdayIndex = $lesson->day_of_week ?? $lesson->lesson_date?->dayOfWeekIso;
                                    $weekdayName = $weekdayIndex ? ($weekdayNames[$weekdayIndex] ?? null) : null;
                                    $startTime = $formatTime($lesson->start_time);
                                    $endTime = $formatTime($lesson->end_time);
                                @endphp
                                <li class="lesson-list__item {{ $lesson->id === $highlightLessonId ? 'active' : '' }}">
                                    <div class="d-flex gap-16 align-items-start">
                                        <span class="circle w-32 h-32 rounded-pill bg-white border border-main-100 flex-center text-13 fw-semibold text-gray-500">
                                            {{ $loop->iteration }}
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="d-flex flex-wrap gap-12 justify-content-between align-items-start">
                                                <div>
                                                    <div class="text-sm text-gray-400">
                                                        {{ $weekdayName ? $weekdayName . ',' : '' }}
                                                        {{ $lesson->formatted_date ?: 'Дата не указана' }}
                                                    </div>
                                                    <div class="fw-semibold text-gray-900">{{ $lesson->subject ?? 'Занятие' }}</div>
                                                    <div class="text-13 text-gray-300">
                                                        @if ($startTime)
                                                            {{ $startTime }}–{{ $endTime ?? '—' }}
                                                        @else
                                                            Время не указано
                                                        @endif
                                                        @if ($lesson->classroom)
                                                            • {{ $lesson->classroom }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="badge rounded-pill {{ $lesson->is_completed ? 'bg-success-100 text-success-600' : 'bg-main-100 text-main-600' }}">
                                                    {{ $lesson->status }}
                                                </span>
                                            </div>

                                            @if ($lesson->topic)
                                                <div class="mt-12 text-13 text-gray-400">
                                                    <span class="text-gray-300 fw-medium">Тема:</span> {{ $lesson->topic }}
                                                </div>
                                            @endif

                                            @if ($lesson->homework)
                                                <div class="mt-8 text-13 text-gray-400">
                                                    <span class="text-gray-300 fw-medium">ДЗ:</span> {{ $lesson->homework }}
                                                </div>
                                            @endif

                                            @if (! $lesson->topic && ! $lesson->homework)
                                                <div class="mt-12 text-13 text-gray-200">Детали занятия ещё не заполнены.</div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-16 mb-24">
        <div class="px-24 pt-24 pb-16 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-12">
                <h5 class="mb-0">Календарно-тематическое планирование (КТП)</h5>
                <span class="text-sm text-gray-300">Дата проставляется автоматически по занятию</span>
            </div>
        </div>
        <div class="px-24 pb-24">
            @if ($group->lessons->isEmpty())
                <p class="text-gray-200 mb-0">После создания занятий заполните темы и домашние задания для КТП.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="text-sm text-gray-400 fw-medium">Дата</th>
                                <th class="text-sm text-gray-400 fw-medium">Тема урока</th>
                                <th class="text-sm text-gray-400 fw-medium">ДЗ на урок</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group->lessons as $lesson)
                                <tr class="{{ $lesson->id === $highlightLessonId ? 'table-active' : '' }}">
                                    <td class="text-nowrap">{{ $lesson->formatted_date ?: '—' }}</td>
                                    <td>{{ $lesson->topic ?: 'Тема не заполнена' }}</td>
                                    <td>{{ $lesson->homework ?: 'ДЗ не назначено' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-16 p-24">
        <h5 class="mb-16">Ученики группы ({{ $group->students->count() }})</h5>
        @if ($group->students->isEmpty())
            <p class="text-gray-200 mb-0">В группу ещё не добавлены ученики.</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Телефон</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group->students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->phone ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
