@extends('welcome')

@section('title', $student->name)
@section('page_title', $student->name)
@section('page_subtitle', 'Ученик')

@section('page_actions')
    <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-gray">Редактировать</a>
    <a href="{{ route('students.password.edit', $student) }}" class="btn btn-outline-gray">Изменить пароль</a>
    <a href="{{ route('students.index') }}" class="btn btn-outline-gray">К списку</a>
@endsection

@section('content')
    @include('partials.flash')

    <div class="bg-white border border-gray-100 rounded-16 p-24">
        <h5 class="mb-16">Основные данные</h5>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Email</div>
                <div class="fw-semibold">{{ $student->email }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Телефон</div>
                <div>{{ $student->phone ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">ИИН</div>
                <div>{{ $student->iin ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Дата рождения</div>
                <div>{{ optional($student->birth_date)->format('d.m.Y') ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-gray-400 text-sm">Приказ</div>
                @if ($order)
                    <div class="fw-semibold">№ {{ $order->order_number }} от {{ optional($order->order_date)->format('d.m.Y') }}</div>
                    <div class="text-sm text-gray-300">{{ $orderTypes[$order->order_type] ?? '—' }}</div>
                @else
                    <div>—</div>
                @endif
            </div>
        </div>
    </div>
@endsection
