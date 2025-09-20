@extends('welcome')

@section('title', 'Курсы')
@section('page_title', 'Курсы')

@section('breadcrumb')
    <ul class="flex-align gap-4">
        <li><a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Главная</a></li>
        <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
        <li><span class="text-main-600 fw-normal text-15">Курсы</span></li>
    </ul>
@endsection

@section('page_actions')
    <a href="{{ route('courses.create') }}" class="btn btn-main">Добавить курс</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="card overflow-hidden">
        <div class="card-body p-0 overflow-x-auto">
            <table id="coursesTable" class="table table-striped dataTable w-100">
                <colgroup>
                    <col data-dt-column="0" style="width: 280px;">
                    <col data-dt-column="1" style="width: 200px;">
                    <col data-dt-column="2" style="width: 120px;">
                    <col data-dt-column="3" style="width: 120px;">
                    <col data-dt-column="4" style="width: 140px;">
                    <col data-dt-column="5" style="width: 200px;">
                </colgroup>
                <thead>
                    <tr role="row">
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="0" tabindex="0">
                            <span class="dt-column-title" role="button">Курс</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="1" tabindex="0">
                            <span class="dt-column-title" role="button">Школа</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-type-numeric dt-orderable-asc dt-orderable-desc" data-dt-column="2" tabindex="0">
                            <span class="dt-column-title" role="button">Группы</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-type-numeric dt-orderable-asc dt-orderable-desc" data-dt-column="3" tabindex="0">
                            <span class="dt-column-title" role="button">Ученики</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="4" tabindex="0">
                            <span class="dt-column-title" role="button">Статус</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-none text-end" data-dt-column="5">
                            <span class="dt-column-title">Действия</span>
                            <span class="dt-column-order"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <a href="{{ route('courses.show', $course) }}" class="h6 mb-0 fw-medium text-gray-900">{{ $course->name }}</a>
                                    <div class="text-13 text-gray-300 text-line-2">{{ $course->description ?: 'Описание не заполнено' }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $course->school?->name ?? '—' }}</span>
                            </td>
                            <td class="dt-type-numeric">
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $course->groups_count }}</span>
                            </td>
                            <td class="dt-type-numeric">
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $course->students_count }}</span>
                            </td>
                            <td>
                                <span class="text-13 py-2 px-8 {{ $course->is_active ? 'bg-success-50 text-success-600' : 'bg-gray-100 text-gray-500' }} d-inline-flex align-items-center gap-8 rounded-pill">
                                    <span class="w-6 h-6 {{ $course->is_active ? 'bg-success-600' : 'bg-gray-400' }} rounded-circle flex-shrink-0"></span>
                                    {{ $course->is_active ? 'Активен' : 'Архив' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-10">
                                    <a href="{{ route('courses.edit', $course) }}" class="text-main-600 text-20 hover-text-main-700" title="Редактировать">
                                        <i class="ph ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Удалить курс «{{ $course->name }}»?');">
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
                            <td colspan="6" class="text-center py-4 text-gray-200">Курсы пока не добавлены.</td>
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
            const tableEl = document.querySelector('#coursesTable');
            if (!tableEl || typeof DataTable === 'undefined') {
                return;
            }

            new DataTable(tableEl, {
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [5] },
                ],
                language: {
                    search: 'Поиск:',
                    paginate: {
                        previous: 'Назад',
                        next: 'Вперёд',
                    },
                    zeroRecords: 'Курсы не найдены',
                },
            });
        });
    </script>
@endpush
