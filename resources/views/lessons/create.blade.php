@extends('welcome')

@section('title', 'Новое занятие')
@section('page_title', 'Создание занятия')
@section('page_subtitle', $group->name)

@section('page_actions')
    <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'lessons']) }}" class="btn btn-outline-gray">Назад к занятиям</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('groups.lessons.store', $group) }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf

        <input type="hidden" name="week_start" value="{{ old('week_start', $weekStart) }}">

                <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">День недели *</label>
                <select name="day_of_week" class="form-select" required>
                    @foreach ($weekdayNames as $value => $label)
                        <option value="{{ $value }}" @selected(old('day_of_week', $defaultDay ?? 1) == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Время начала *</label>
                <input type="time" name="start_time" value="{{ old('start_time', $defaultStart) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Время окончания</label>
                <input type="time" name="end_time" value="{{ old('end_time', $defaultEnd) }}" class="form-control">
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

            <input type="hidden" name="subject" value="{{ old('subject', $defaultSubject) }}">
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('groups.show', ['group' => $group->id, 'tab' => 'lessons']) }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
