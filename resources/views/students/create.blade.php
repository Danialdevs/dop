@extends('welcome')

@section('title', 'Новый ученик')
@section('page_title', 'Добавление ученика')

@section('page_actions')
    <a href="{{ route('students.index') }}" class="btn btn-outline-gray">Назад к списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('students.store') }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ФИО *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Пароль *</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Подтверждение пароля *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Телефон</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="+7 700 000 00 00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Дата рождения</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Школа</label>
                <select name="school_id" class="form-select">
                    <option value="">Не указано</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="student-active" value="1" @checked(old('is_active', true))>
                    <label class="form-check-label" for="student-active">Ученик активен</label>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Группы</label>
                <select name="group_ids[]" class="form-select" multiple size="6">
                    @php
                        $selectedGroups = collect(old('group_ids', []));
                    @endphp
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected($selectedGroups->contains($group->id))>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Курсы</label>
                <select name="course_ids[]" class="form-select" multiple size="6">
                    @php
                        $selectedCourses = collect(old('course_ids', []));
                    @endphp
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected($selectedCourses->contains($course->id))>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
