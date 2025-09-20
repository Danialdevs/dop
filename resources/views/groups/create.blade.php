@extends('welcome')

@section('title', 'Новая группа')
@section('page_title', 'Создание группы')

@section('page_actions')
    <a href="{{ route('groups.index') }}" class="btn btn-outline-gray">Назад к списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('groups.store') }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Название *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Курс *</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Выберите курс</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Преподаватель</label>
                <select name="teacher_id" class="form-select">
                    <option value="">Не назначен</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Учебный год</label>
                <select name="academic_year_id" class="form-select">
                    <option value="">Не выбран</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Язык обучения *</label>
                <select name="language" class="form-select" required>
                    @php
                        $languageOptions = ['русский' => 'Русский', 'казахский' => 'Казахский', 'английский' => 'Английский'];
                    @endphp
                    @foreach ($languageOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('language', 'русский') == $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Активность</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="group-active" value="1" @checked(old('is_active', true))>
                    <label class="form-check-label" for="group-active">Группа активна</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Описание</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Кратко опишите группу">{{ old('description') }}</textarea>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Ученики</label>
                <select name="student_ids[]" class="form-select" multiple size="6">
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected(collect(old('student_ids'))->contains($student->id))>
                            {{ $student->name }} — {{ $student->email }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Удерживайте Ctrl / Cmd для выбора нескольких учеников.</div>
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('groups.index') }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
