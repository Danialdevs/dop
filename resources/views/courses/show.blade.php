@extends('welcome')

@section('title', $course->name)
@section('page_title', $course->name)
@section('page_subtitle', 'Курс')

@section('page_actions')
    <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-gray">Редактировать</a>
    <a href="{{ route('courses.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="bg-white border border-gray-100 rounded-16 p-24 mb-24">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Школа</div>
                <div class="fw-semibold">{{ $course->school?->name ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Статус</div>
                <div>
                    <span class="badge rounded-pill {{ $course->is_active ? 'bg-success-100 text-success-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $course->is_active ? 'Активен' : 'Архив' }}
                    </span>
                </div>
            </div>
            <div class="col-12">
                <div class="text-gray-400 text-sm mb-1">Описание</div>
                <div>{{ $course->description ?: 'Описание не заполнено.' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-24">
        <div class="col-lg-6">
            <div class="bg-white border border-gray-100 rounded-16 p-24 h-100">
                <h5 class="mb-16">Группы курса</h5>
                @if ($course->groups->isEmpty())
                    <p class="text-gray-200 mb-0">Группы отсутствуют.</p>
                @else
                    <ul class="list-unstyled mb-0 d-grid gap-12">
                        @foreach ($course->groups as $group)
                            <li>
                                <div class="fw-semibold">{{ $group->name }}</div>
                                <div class="text-sm text-gray-300">Преподаватель: {{ $group->teacher?->name ?? 'Не назначен' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="bg-white border border-gray-100 rounded-16 p-24 h-100">
                <h5 class="mb-16">Ученики курса</h5>
                @if ($course->students->isEmpty())
                    <p class="text-gray-200 mb-0">Ученики не назначены.</p>
                @else
                    <ul class="list-unstyled mb-0 d-grid gap-8">
                        @foreach ($course->students as $student)
                            <li class="d-flex justify-content-between">
                                <span>{{ $student->name }}</span>
                                <span class="text-gray-300 text-sm">{{ $student->email }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
