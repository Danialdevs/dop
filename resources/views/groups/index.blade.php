@extends('welcome')

@section('title', 'Группы')
@section('page_title', 'Группы')

@section('breadcrumb')
    <ul class="flex-align gap-4">
        <li><a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Главная</a></li>
        <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
        <li><span class="text-main-600 fw-normal text-15">Группы</span></li>
    </ul>
@endsection

@section('page_actions')
    <a href="{{ route('groups.create') }}" class="btn btn-main">Создать группу</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="card overflow-hidden">
        <div class="card-body p-0 overflow-x-auto">
            <table id="groupsTable" class="table table-striped dataTable w-100">
                <colgroup>
                    <col data-dt-column="0" style="width: 234.672px;">
                    <col data-dt-column="1" style="width: 234.672px;">
                    <col data-dt-column="2" style="width: 234.672px;">
                </colgroup>
                <thead>
                    <tr role="row">
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="0" tabindex="0">
                            <span class="dt-column-title" role="button">Группа</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-asc dt-orderable-desc" data-dt-column="1" tabindex="0">
                            <span class="dt-column-title" role="button">Курс</span>
                            <span class="dt-column-order"></span>
                        </th>
                        <th class="h6 text-gray-300 dt-orderable-none text-end" data-dt-column="2">
                            <span class="dt-column-title">Действия</span>
                            <span class="dt-column-order"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <a href="{{ route('groups.show', $group) }}" class="h6 mb-0 fw-medium text-gray-900">{{ $group->name }}</a>
                                    @if ($group->description)
                                        <div class="text-13 text-gray-300 text-line-2">{{ $group->description }}</div>
                                    @endif
                                    <div class="text-13 text-gray-300">Язык: {{ ucfirst($group->language) }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="h6 mb-0 fw-medium text-gray-300">{{ $group->course?->name ?? '—' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-10">
                                    <a href="{{ route('groups.edit', $group) }}" class="text-main-600 text-20 hover-text-main-700" title="Редактировать">
                                        <i class="ph ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Удалить группу «{{ $group->name }}»?');">
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
                            <td colspan="3" class="text-center py-4 text-gray-200">Группы пока не созданы.</td>
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
            const tableEl = document.querySelector('#groupsTable');
            if (!tableEl || typeof DataTable === 'undefined') {
                return;
            }

            new DataTable(tableEl, {
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [2] },
                ],
                language: {
                    search: 'Поиск:',
                    paginate: {
                        previous: 'Назад',
                        next: 'Вперёд',
                    },
                    zeroRecords: 'Группы не найдены',
                },
            });
        });
    </script>
@endpush
