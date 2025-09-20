@extends('welcome')

@section('title', 'Смена пароля учителя')
@section('page_title', 'Смена пароля')
@section('page_subtitle', $teacher->name)

@section('page_actions')
    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-gray">К профилю</a>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('teachers.password.update', $teacher) }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Новый пароль *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Подтверждение пароля *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
