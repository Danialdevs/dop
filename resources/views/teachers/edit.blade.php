@extends('welcome')

@section('title', 'Редактирование учителя')
@section('page_title', 'Редактирование учителя')

@section('page_actions')
    <a href="{{ route('teachers.password.edit', $teacher) }}" class="btn btn-outline-gray">Изменить пароль</a>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-gray">Назад к списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('teachers.update', $teacher) }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ФИО *</label>
                <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">ИИН *</label>
                <input type="text" name="iin" value="{{ old('iin', $teacher->iin) }}" class="form-control" inputmode="numeric" pattern="\d{12}" maxlength="12" placeholder="000000000000" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email *</label>
                <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Телефон</label>
                <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control" placeholder="+7 700 000 00 00">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Дата рождения</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', optional($teacher->birth_date)->format('Y-m-d')) }}" class="form-control">
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
