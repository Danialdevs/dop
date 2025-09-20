@extends('welcome')

@section('title', 'Редактирование курса')
@section('page_title', 'Редактирование курса')

@section('page_actions')
    <a href="{{ route('courses.index') }}" class="btn btn-outline-gray">Назад к списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('courses.update', $course) }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Название *</label>
                <input type="text" name="name" value="{{ old('name', $course->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Школа *</label>
                <select name="school_id" class="form-select" required>
                    <option value="">Выберите школу</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected(old('school_id', $course->school_id) == $school->id)>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Описание</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Краткое описание курса">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="course-active" value="1" @checked(old('is_active', $course->is_active))>
                    <label class="form-check-label" for="course-active">Курс активен</label>
                </div>
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
