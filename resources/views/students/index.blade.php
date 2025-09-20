@extends('welcome')

@section('title', 'Ученики')
@section('page_title', 'Ученики')

@section('breadcrumb')
    <ul class="flex-align gap-4">
        <li><a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Главная</a></li>
        <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
        <li><span class="text-main-600 fw-normal text-15">Ученики</span></li>
    </ul>
@endsection

@section('page_actions')
    <a href="{{ route('students.create') }}" class="btn btn-main">Добавить ученика</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="card overflow-hidden">
        <div class="card-body p-0 overflow-x-auto">
            <table id="studentsTable" class="table table-striped dataTable w-100">
                <colgroup>
                    <col data-dt-column="0" style="width: 260px;">
                    <col data-dt-column="1" style="width: 220px;">
                    <col data-dt-column="2" style="width: 140px;">
                    <col data-dt-column="3" style="width: 120px;">
                    <col data-dt-column="4" style="width: 120px;">
                    <col data-dt-column="5" style="width: 140px;">
                    <col data-dt-column="6" style="width: 120px;">
                </colgroup>
                <thead>
                    <tr role="row">
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="0" tabindex="0">
                            <span class="dt-column-title" role="button">Ученики</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="1" tabindex="0">
                            <span class="dt-column-title" role="button">Email</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="2" tabindex="0">
                            <span class="dt-column-title" role="button">Телефон</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-type-numeric dt-orderable-asc dt-orderable-desc" data-dt-column="3" tabindex="0">
                            <span class="dt-column-title" role="button">Группы</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-type-numeric dt-orderable-asc dt-orderable-desc" data-dt-column="4" tabindex="0">
                            <span class="dt-column-title" role="button">Курсы</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="5" tabindex="0">
                            <span class="dt-column-title" role="button">Статус</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-none text-end" data-dt-column="6">
                            <span class="dt-column-title">Действия</span>
                            <span class="dt-column-order"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        @php
                            $initial = mb_strtoupper(mb_substr($student->name, 0, 1));
                        @endphp
                        <tr>
                            <td>
                                <div class="flex-align gap-8">
                                    <span class="w-40 h-40 rounded-circle bg-main-50 text-main-600 fw-semibold flex-center text-sm">
                                        {{ $initial }}
                                    </span>
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="h6 mb-0 fw-medium text-gray-900">{{ $student->name }}</a>
                                        <div class="text-13 text-gray-300">{{ $student->school?->name ?? 'Школа не указана' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->email }}</span>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->phone ?? '—' }}</span>
                            </td>
                            <td class="dt-type-numeric">
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->student_groups_count }}</span>
                            </td>
                            <td class="dt-type-numeric">
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->courses_count }}</span>
                            </td>
                            <td>
                                <span class="text-13 py-2 px-8 {{ $student->is_active ? 'bg-success-50 text-success-600' : 'bg-gray-100 text-gray-500' }} d-inline-flex align-items-center gap-8 rounded-pill">
                                    <span class="w-6 h-6 {{ $student->is_active ? 'bg-success-600' : 'bg-gray-400' }} rounded-circle flex-shrink-0"></span>
                                    {{ $student->is_active ? 'Активен' : 'Архив' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-10">
                                    <a href="{{ route('students.edit', $student) }}" class="text-main-600 text-20 hover-text-main-700" title="Редактировать">
                                        <i class="ph ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Удалить ученика «{{ $student->name }}»?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 text-20 bg-transparent border-0 p-0 hover-text-danger-700" title="Удалить">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-200">Ученики пока не добавлены.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableEl = document.querySelector('#studentsTable');
            if (!tableEl || typeof DataTable === 'undefined') {
                return;
            }

            new DataTable(tableEl, {
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [6] },
                ],
                language: {
                    search: 'Поиск:',
                    paginate: {
                        previous: 'Назад',
                        next: 'Вперёд',
                    },
                    zeroRecords: 'Нет подходящих записей',
                },
            });
        });
    </script>
@endpush
