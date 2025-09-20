@extends('welcome')

@section('title', $student->name)
@section('page_title', $student->name)
@section('page_subtitle', 'Ученик')

@section('page_actions')
    <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-gray">Редактировать</a>
    <a href="{{ route('students.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="bg-white border border-gray-100 rounded-16 p-24 mb-24">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Email</div>
                <div class="fw-semibold">{{ $student->email }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Телефон</div>
                <div>{{ $student->phone ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Дата рождения</div>
                <div>{{ optional($student->birth_date)->format('d.m.Y') ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Школа</div>
                <div>{{ $student->school?->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Статус</div>
                <div>
                    <span class="badge rounded-pill {{ $student->is_active ? 'bg-success-100 text-success-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $student->is_active ? 'Активен' : 'Архив' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-24">
        <div class="col-lg-6">
            <div class="bg-white border border-gray-100 rounded-16 p-24 h-100">
                <h5 class="mb-16">Группы ({{ $student->studentGroups->count() }})</h5>
                @if ($student->studentGroups->isEmpty())
                    <p class="text-gray-200 mb-0">Ученику не назначены группы.</p>
                @else
                    <ul class="list-unstyled mb-0 d-grid gap-12">
                        @foreach ($student->studentGroups as $group)
                            <li>
                                <div class="fw-semibold">{{ $group->name }}</div>
                                <div class="text-sm text-gray-300">Курс: {{ $group->course?->name ?? '—' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="bg-white border border-gray-100 rounded-16 p-24 h-100">
                <h5 class="mb-16">Курсы ({{ $student->courses->count() }})</h5>
                @if ($student->courses->isEmpty())
                    <p class="text-gray-200 mb-0">Ученику не назначены курсы.</p>
                @else
                    <ul class="list-unstyled mb-0 d-grid gap-8">
                        @foreach ($student->courses as $course)
                            <li class="d-flex justify-content-between">
                                <span>{{ $course->name }}</span>
                                <span class="text-gray-300 text-sm">{{ $course->school?->name ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
