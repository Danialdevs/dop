@extends('welcome')

@php
    use Illuminate\Support\Str;
    $activeTab = $activeTab ?? request('tab', 'overview');
@endphp

@push('styles')
    <style>
        .schedule-table th {
            min-width: 140px;
            background: #f8fafc;
            border-color: #e2e8f0 !important;
        }

        .schedule-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        }

        .schedule-empty {
            padding: 16px 0;
            border: 1px dashed #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
        }

        .schedule-toolbar .btn.btn-outline-gray.btn-sm {
            border-color: #d0d7e2;
        }
    </style>
@endpush

@section('title')
    {{ $group->name }}
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('lessonModal');
            if (!modalEl) return;

            modalEl.addEventListener('show.bs.modal', (event) => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const dayField = modalEl.querySelector('.lesson-day-field');
                const startField = modalEl.querySelector('.lesson-start-field');
                const endField = modalEl.querySelector('.lesson-end-field');
                const subjectField = modalEl.querySelector('.lesson-subject-field');

                if (dayField && trigger.dataset.day) {
                    dayField.value = trigger.dataset.day;
                }

                if (startField) {
                    startField.value = trigger.dataset.start || '';
                }

                if (endField) {
                    endField.value = trigger.dataset.end || '';
                }

                if (subjectField) {
                    subjectField.value = trigger.dataset.subject || '';
                }
            });

            @if ($errors->any() && old('week_start') && $activeTab === 'lessons')
                const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                bootstrapModal.show();
            @endif
        });
    </script>
@endpush

@section('page_title')
    {{ $group->name }}
@endsection

@section('page_subtitle')
    Группа
@endsection

@section('page_actions')
    <div class="d-flex gap-8 flex-wrap">
       
        <a href="{{ route('groups.index') }}" class="btn btn-outline-gray">К списку</a>
    </div>
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
        <div class="px-24 pt-24 pb-12 border-bottom">
            <ul class="nav nav-tabs gap-2 flex-wrap" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.show', $group) }}" class="nav-link text-sm fw-semibold {{ $activeTab === 'overview' ? 'active' : '' }}">
                        Обзор
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'lessons']) }}" class="nav-link text-sm fw-semibold {{ $activeTab === 'lessons' ? 'active' : '' }}">
                        Занятия
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'students']) }}" class="nav-link text-sm fw-semibold {{ $activeTab === 'students' ? 'active' : '' }}">
                        Ученики
                    </a>
                </li>
                 <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'ktp']) }}" class="nav-link text-sm fw-semibold {{ $activeTab === 'ktp' ? 'active' : '' }}">
                        Тематическое планирование
                    </a>
                </li>
                 <li class="nav-item" role="presentation">
                    <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'students']) }}" class="nav-link text-sm fw-semibold {{ $activeTab === 'students' ? 'active' : '' }}">
                        Журнал
                    </a>
                </li>
            </ul>
        </div>

        <div class="px-24 pb-24">
            @if ($activeTab === 'overview')
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
                        <div class="text-gray-400 text-sm">Язык обучения</div>
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
                @elseif ($activeTab === 'ktp')
                <h5 class="mb-16">Календарно-тематическое планирование</h5>
               <table class="table table-bordered">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Тема</th>
            <th>Домашнее задание</th>
        </tr>
    </thead>
    <tbody>
            @foreach ($group->lessons as $lesson)

    <tr>
                    <td><a href="#" class="editable">{{ $lesson->formatted_date ?: '—' }}</a></td>
        <td><a href="#" class="editable">{{ $lesson->topic ?: 'Тема не заполнена' }}</a></td>
        <td><a href="#" class="editable">{{ $lesson->homework ?: 'ДЗ не назначено' }}</a></td>

    
      </tr>
                                  @endforeach
        
    </tbody>
</table>
<script>
document.addEventListener('click', function(e) {
  // клик по "editable"
  if (e.target.classList.contains('editable')) {
    e.preventDefault();
    const link = e.target;
    const oldText = link.textContent.trim();

    // создаём input
    const input = document.createElement('input');
    input.type = 'text';
    input.value = oldText;
    input.className = 'form-control form-control-sm';
    link.replaceWith(input);
    input.focus();

    // обработка Enter / Esc / blur
    input.addEventListener('keydown', function(ev) {
      if (ev.key === 'Enter') {
        saveValue(input);
      } else if (ev.key === 'Escape') {
        cancelEdit(input, oldText);
      }
    });
    input.addEventListener('blur', function() {
      saveValue(input);
    });
  }

  // клик по "+"
  if (e.target.classList.contains('addRow')) {
    const tbody = document.getElementById('lessonsTable');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><a href="#" class="editable">Новая дата</a></td>
      <td><a href="#" class="editable">Новая тема</a></td>
      <td><a href="#" class="editable">Новое ДЗ</a></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-success addRow">+</button>
      </td>
    `;
    tbody.appendChild(row);
  }
});

function saveValue(input) {
  const text = input.value.trim() || '—';
  const link = document.createElement('a');
  link.href = '#';
  link.className = 'editable';
  link.textContent = text;
  input.replaceWith(link);
}

function cancelEdit(input, oldText) {
  const link = document.createElement('a');
  link.href = '#';
  link.className = 'editable';
  link.textContent = oldText;
  input.replaceWith(link);
}
</script>
                @elseif ($activeTab === 'lessons')
                <div class="schedule-toolbar border border-gray-100 rounded-12 p-20 mb-24">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-12">
                        <div class="d-flex align-items-center gap-8">
                            <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $prevWeekStart->toDateString()]) }}" class="btn btn-outline-gray btn-sm p-6"><i class="ph ph-caret-left"></i></a>
                                                        <div class="fw-semibold text-gray-900">{{ $weekStart->translatedFormat('d MMMM') }} – {{ $weekEnd->translatedFormat('d MMMM Y') }} (Всего {{ $currentWeekCount }} занят{{ $currentWeekCount === 1 ? 'ие' : ($currentWeekCount >= 2 && $currentWeekCount <= 4 ? 'ия' : 'ий') }})</div>

                            <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'lessons', 'week' => $nextWeekStart->toDateString()]) }}" class="btn btn-outline-gray btn-sm p-6"><i class="ph ph-caret-right"></i></a>
                        </div>
                       
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-16 mt-16">
                        <button type="button" class="btn btn-outline-gray btn-sm p-6" data-bs-toggle="modal" data-bs-target="#copyWeekModal">Копировать</button>
                        <form action="{{ route('groups.lessons.clear-week', $group) }}" method="POST" class="d-flex align-items-end gap-8" onsubmit="return confirm('Очистить все занятия недели?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                            <button type="submit" class="btn  btn-danger btn-sm p-6">Очистить</button>
                        </form>
            
                    </div>
                </div>

                <div class="table-responsive mb-24">
                    <table class="table table-bordered align-middle text-sm schedule-table">
                        <thead class="bg-main-50">
                            <tr>
                                @foreach ($weekDays as $day)
                                    <th class="text-center {{ in_array($day->dayOfWeekIso, [6,7]) ? 'text-danger' : '' }}">
                                        <div class="fw-semibold">{{ Str::ucfirst($day->translatedFormat('D')) }}</div>
                                        <div class="text-13 text-gray-400">{{ $day->translatedFormat('d MMM') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($weekDays as $day)
                                    @php($dateKey = $day->toDateString())
                                    <td class="align-top p-12">
                                        @forelse ($currentWeekLessons->get($dateKey, collect()) as $lesson)
                                            <div class="schedule-card mb-12">
                                                <div class="fw-semibold text-main-600">{{ $lesson->subject }}</div>
                                                <div class="text-13 text-gray-500">{{ $formatTime($lesson->start_time) ?? '—' }}@if ($lesson->end_time) – {{ $formatTime($lesson->end_time) }}@endif</div>
                                                <div class="text-13 text-gray-400">{{ $lesson->teacher?->name ?? $group->teacher?->name ?? 'Преподаватель не назначен' }}</div>
                                                <div class="text-13 text-gray-300">{{ $lesson->classroom ?: 'Нет кабинета' }}</div>
                                                @if ($lesson->homework)
                                                    <div class="text-13 text-gray-300 mt-6">ДЗ: {{ Str::limit($lesson->homework, 70) }}</div>
                                                @endif
                                                <div class="d-flex gap-8 mt-8">
                                                    <a href="{{ route('groups.lessons.edit', [$group, $lesson]) }}" class="text-main-600 text-18" title="Редактировать"><i class="ph ph-pencil"></i></a>
                                                    <form action="{{ route('groups.lessons.destroy', [$group, $lesson]) }}" method="POST" onsubmit="return confirm('Удалить занятие?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-danger-600 text-18 bg-transparent border-0" title="Удалить"><i class="ph ph-trash"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="schedule-empty text-center text-13 text-gray-300">Нет занятий</div>
                                        @endforelse
                                        <div class="text-center mt-10">
                                            <button type="button" class="btn btn-link text-main-600 p-0" data-bs-toggle="modal" data-bs-target="#lessonModal"
                                                data-day="{{ $day->dayOfWeekIso }}" data-start="" data-end="" data-subject="" title="Добавить занятие">
                                                <i class="ph ph-plus-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="lessonModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Добавить занятие</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('groups.lessons.store', $group) }}" method="POST">
                                @csrf
                                <input type="hidden" name="week_start" value="{{ old('week_start', $weekStart->toDateString()) }}">
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">День недели *</label>
                                            <select name="day_of_week" class="form-select lesson-day-field" required>
                                                @foreach ($weekdayNames as $value => $label)
                                                    <option value="{{ $value }}" @selected(old('day_of_week') == $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Начало *</label>
                                            <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-control lesson-start-field" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Окончание</label>
                                            <input type="time" name="end_time" value="{{ old('end_time') }}" class="form-control lesson-end-field">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Преподаватель</label>
                                            <select name="teacher_id" class="form-select">
                                                <option value="">По умолчанию ({{ $group->teacher?->name ?? 'не назначен' }})</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Аудитория</label>
                                            <input type="text" name="classroom" value="{{ old('classroom') }}" class="form-control" placeholder="Каб. 201">
                                        </div>
                                        <input type="hidden" name="subject" value="{{ old('subject', $group->name . ' занятие') }}" class="lesson-subject-field">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-gray" data-bs-dismiss="modal">Отмена</button>
                                    <button type="submit" class="btn btn-main">Сохранить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="copyWeekModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Копировать неделю</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('groups.lessons.copy-week', $group) }}" method="POST">
                                @csrf
                                <input type="hidden" name="source_week_start" value="{{ $weekStart->toDateString() }}">
                                <div class="modal-body">
                                    <p class="text-sm text-gray-500">Выберите период, куда планируется скопировать расписание.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">С *</label>
                                            <input type="date" name="target_week_start" value="{{ old('target_week_start', $nextWeekStart->toDateString()) }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">По *</label>
                                            <input type="date" name="target_week_end" value="{{ old('target_week_end', $nextWeekStart->copy()->addWeek()->toDateString()) }}" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-gray" data-bs-dismiss="modal">Отмена</button>
                                    <button type="submit" class="btn btn-main">Скопировать</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-12">
                    <div class="px-20 pt-20 pb-12 border-bottom">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-10">
                            <h5 class="mb-0">Календарно-тематическое планирование (КТП)</h5>
                            <span class="text-sm text-gray-300">Дата проставляется автоматически по занятию</span>
                        </div>
                    </div>
                    <div class="px-20 py-20">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
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
                    </div>
                </div>
            @elseif ($activeTab === 'students')
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
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('lessonModal');
            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', (event) => {
                    const trigger = event.relatedTarget;
                    const dayField = modalEl.querySelector('.lesson-day-field');
                    const startField = modalEl.querySelector('.lesson-start-field');
                    const endField = modalEl.querySelector('.lesson-end-field');
                    const subjectField = modalEl.querySelector('.lesson-subject-field');

                    if (trigger && trigger.dataset.day && dayField) {
                        dayField.value = trigger.dataset.day;
                    }

                    if (trigger && trigger.dataset.start && startField) {
                        startField.value = trigger.dataset.start;
                    }

                    if (trigger && trigger.dataset.end && endField) {
                        endField.value = trigger.dataset.end;
                    }

                    if (trigger && trigger.dataset.subject && subjectField) {
                        subjectField.value = trigger.dataset.subject;
                    }
                });

                @if ($errors->any() && old('week_start') && $activeTab === 'lessons')
                    const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bootstrapModal.show();
                @endif
            }

            const copyModal = document.getElementById('copyWeekModal');
            if (copyModal && {{ ($errors->has('target_week_start') || $errors->has('source_week_start') || $errors->has('target_week_end')) ? 'true' : 'false' }}) {
                const bootstrapCopyModal = bootstrap.Modal.getOrCreateInstance(copyModal);
                bootstrapCopyModal.show();
            }
        });
    </script>
@endpush
