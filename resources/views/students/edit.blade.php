@extends('welcome')

@section('title', 'Редактирование ученика')
@section('page_title', 'Редактирование ученика')

@section('page_actions')
    <a href="{{ route('students.password.edit', $student) }}" class="btn btn-outline-gray">Изменить пароль</a>
    <a href="{{ route('students.index') }}" class="btn btn-outline-gray">Назад к списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <form action="{{ route('students.update', $student) }}" method="POST" class="bg-white border border-gray-100 rounded-16 p-24">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ФИО *</label>
                <input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">ИИН *</label>
                <input type="text" name="iin" value="{{ old('iin', $student->iin) }}" class="form-control" inputmode="numeric" pattern="\d{12}" maxlength="12" placeholder="000000000000" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email *</label>
                <input type="email" name="email" value="{{ old('email', $student->email) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Телефон</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="form-control" placeholder="+7 700 000 00 00">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Дата рождения</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}" class="form-control">
            </div>
        </div>

        <div class="mt-24 d-flex gap-12">
            <button type="submit" class="btn btn-main">Сохранить</button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-gray">Отмена</a>
        </div>
    </form>
@endsection
