@php
    $flashMessages = [];

    if (session('status')) {
        $flashMessages[] = [
            'type' => 'success',
            'title' => 'Готово',
            'messages' => [session('status')],
        ];
    }

    if ($errors->any()) {
        $flashMessages[] = [
            'type' => 'danger',
            'title' => 'Проверьте введённые данные',
            'messages' => $errors->all(),
        ];
    }
@endphp

@if (! empty($flashMessages))
    @push('scripts')
        <script>
            window.__FLASH_QUEUE = (window.__FLASH_QUEUE || []).concat(@json($flashMessages, JSON_UNESCAPED_UNICODE));
        </script>
    @endpush
@endif
