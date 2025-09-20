@extends('welcome')

@section('title', $group->name)
@section('page_title', $group->name)
@section('page_subtitle', 'Группа')

@section('page_actions')
    <a href="{{ route('groups.edit', $group) }}" class="btn btn-outline-gray">Редактировать</a>
    <a href="{{ route('groups.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="bg-white border border-gray-100 rounded-16 p-24 mb-24">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Курс</div>
                <div class="fw-semibold">{{ $group->course?->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Преподаватель</div>
                <div>{{ $group->teacher?->name ?? 'Не назначен' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Учебный год</div>
                <div>{{ $group->academicYear?->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Язык</div>
                <div>{{ $group->language }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-gray-400 text-sm">Статус</div>
                <div>
                    <span class="badge rounded-pill {{ $group->is_active ? 'bg-success-100 text-success-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $group->is_active ? 'Активна' : 'Архив' }}
                    </span>
                </div>
            </div>
            <div class="col-12">
                <div class="text-gray-400 text-sm mb-1">Описание</div>
                <div>{{ $group->description ?: 'Описание не заполнено.' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-16 p-24">
        <h5 class="mb-16">Ученики группы ({{ $group->students->count() }})</h5>
        @if ($group->students->isEmpty())
            <p class="text-gray-200 mb-0">В группу еще не добавлены ученики.</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Телефон</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group->students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->phone ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
