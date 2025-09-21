@extends('welcome')

@section('title', 'Настройки')
@section('page_title', 'Настройки')
@section('page_subtitle', 'Управляйте названием учреждения и допустимыми системами оценивания')

@section('content')
    @include('partials.flash')

    <div class="row g-24">
        <div class="col-12">
            <form method="POST" action="{{ route('settings.update') }}" class="bg-white border border-gray-100 rounded-16">
                @csrf
                @method('PUT')

                <div class="px-24 pt-24 pb-16 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-12">
                    <div>
                        <h5 class="mb-0">Общие параметры</h5>
                    </div>
                    <button type="submit" class="btn btn-main-600">Сохранить</button>
                </div>

                <div class="px-24 pb-24">
                    <div class="mb-24">
                        <label for="school-name" class="form-label text-sm text-gray-400">Наименование ОУ</label>
                        <input
                            type="text"
                            id="school-name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Например, Гимназия №25"
                            value="{{ old('name', $school?->name) }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-16 d-flex justify-content-between align-items-center flex-wrap gap-12">
                        <div>
                            <h6 class="mb-0">Разрешённые системы оценок</h6>
                            <p class="text-sm text-gray-300 mb-0">Выберите, какие шкалы доступны педагогам при выставлении оценок.</p>
                        </div>
                        @error('grade_systems')
                            <span class="text-sm text-danger-500">{{ $message }}</span>
                        @enderror
                        @error('grade_systems.*')
                            <span class="text-sm text-danger-500">{{ $message }}</span>
                        @enderror
                    </div>

                    @php
                        $activeSystems = collect(old('grade_systems', $selectedSystems->toArray()))->toArray();
                    @endphp
                    <div class="row g-16">
                        @foreach ($gradeSystems as $system)
                            @php
                                $isActive = in_array($system['key'], $activeSystems, true);
                            @endphp
                            <div class="col-12 col-md-6">
                                <div @class(['card', 'h-100', 'border', 'grade-card', 'active' => $isActive]) data-grade-card>
                                    <div class="card-body d-flex flex-column gap-16">
                                        <div class="d-flex justify-content-between align-items-start gap-12">
                                            <div>
                                                <h6 class="mb-1">{{ $system['label'] }}</h6>
                                                <p class="text-sm text-gray-300 mb-0">{{ $system['description'] }}</p>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="grade-{{ $system['key'] }}"
                                                    name="grade_systems[]"
                                                    value="{{ $system['key'] }}"
                                                    @checked($isActive)
                                                >
                                            </div>
                                        </div>

                                        <div class="grade-scale">
                                            @foreach ($system['scale'] as $mark)
                                                <span class="badge">{{ $mark }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-24 pb-24 border-top">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-main-600">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .grade-card {
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            border-color: #e5e7eb;
        }

        .grade-card.active {
            border-color: #c7d2fe;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
        }

        .grade-scale {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .grade-scale .badge {
            border-radius: 999px;
            padding: 6px 12px;
            background-color: #eef2ff;
            color: #4338ca;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('[data-grade-card]');
            cards.forEach((card) => {
                const checkbox = card.querySelector('input[type="checkbox"]');
                if (!checkbox) {
                    return;
                }

                const syncState = () => {
                    card.classList.toggle('active', checkbox.checked);
                };

                checkbox.addEventListener('change', syncState);
                card.addEventListener('click', (event) => {
                    if (event.target.closest('input, label, button, a')) {
                        return;
                    }
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                });

                syncState();
            });
        });
    </script>
@endpush
