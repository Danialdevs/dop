@extends('welcome')

@section('title', $teacher->name)
@section('page_title', $teacher->name)
@section('page_subtitle', 'Учитель')

@section('page_actions')
    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-outline-gray">Редактировать</a>
    <a href="{{ route('teachers.password.edit', $teacher) }}" class="btn btn-outline-gray">Изменить пароль</a>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="bg-white border border-gray-100 rounded-16 p-24">
        <h5 class="mb-16">Основные данные</h5>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Email</div>
                <div class="fw-semibold">{{ $teacher->email }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Телефон</div>
                <div>{{ $teacher->phone ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">ИИН</div>
                <div>{{ $teacher->iin ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Дата рождения</div>
                <div>{{ optional($teacher->birth_date)->format('d.m.Y') ?? '—' }}</div>
            </div>
        </div>
    </div>
@endsection
