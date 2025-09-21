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
                    <col data-dt-column="1" style="width: 180px;">
                    <col data-dt-column="2" style="width: 180px;">
                    <col data-dt-column="3" style="width: 160px;">
                    <col data-dt-column="4" style="width: 120px;">
                </colgroup>
                <thead>
                    <tr role="row">
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="0" tabindex="0">
                            <span class="dt-column-title" role="button">Ученики</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="1" tabindex="0">
                            <span class="dt-column-title" role="button">Телефон</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="2" tabindex="0">
                            <span class="dt-column-title" role="button">ИИН</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="3" tabindex="0">
                            <span class="dt-column-title" role="button">Дата рождения</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-none text-end" data-dt-column="4">
                            <span class="dt-column-title">Действия</span>
                            <span class="dt-column-order"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="{{ route('students.show', $student) }}" class="h6 mb-0 fw-medium text-gray-900">{{ $student->name }}</a>
                                    <span class="text-13 text-gray-300">{{ $student->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->phone ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $student->iin ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ optional($student->birth_date)->format('d.m.Y') ?? '—' }}</span>
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
                            <td colspan="5" class="text-center py-4 text-gray-200">Ученики пока не добавлены.</td>
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
                    { orderable: false, targets: [4] },
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
