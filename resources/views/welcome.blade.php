<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ЖУРНАЛ')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- File Upload -->
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <!-- Plyr -->
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- Full Calendar -->
    <link rel="stylesheet" href="{{ asset('assets/css/full-calendar.css') }}">
    <!-- jQuery UI -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{ asset('assets/css/editor-quill.css') }}">
    <!-- Apex Charts -->
    <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">
    <!-- Calendar -->
    <link rel="stylesheet" href="{{ asset('assets/css/calendar.css') }}">
    <!-- Vector Map -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-jvectormap-2.0.5.css') }}">
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://unpkg.com/phosphor-icons@1.4.2/src/css/icons.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    @stack('styles')
</head>
<body>
    <!--==================== Preloader Start ====================-->
    <div class="preloader">
        <div class="loader"></div>
    </div>
    <!--==================== Preloader End ====================-->

    <!--==================== Sidebar Overlay ====================-->
    <div class="side-overlay"></div>
    <!--==================== Sidebar Overlay End ====================-->

    @php
        $menuItems = [
            [
                'label' => 'Главная',
                'url' => url('/'),
                'icon' => 'ph ph-squares-four',
                'pattern' => '/',
            ],
            [
                'label' => 'Группы',
                'url' => url('/groups'),
                'icon' => 'ph ph-users-four',
                'pattern' => 'groups*',
            ],
            [
                'label' => 'Ученики',
                'url' => url('/students'),
                'icon' => 'ph ph-identification-card',
                'pattern' => 'students*',
            ],
            [
                'label' => 'Курсы',
                'url' => url('/courses'),
                'icon' => 'ph ph-graduation-cap',
                'pattern' => 'courses*',
            ],
        ];
        $currentPath = trim(request()->path(), '/');
    @endphp

    <!-- ============================ Sidebar Start ============================ -->
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn text-gray-500 hover-text-white hover-bg-main-600 text-md w-24 h-24 border border-gray-100 hover-border-main-600 d-xl-none d-flex flex-center rounded-circle position-absolute">
            <i class="ph ph-x"></i>
        </button>

        <a href="{{ url('/') }}" class="sidebar__logo text-center p-20 position-sticky inset-block-start-0 bg-white w-100 z-1 pb-10">
            <span class="d-block text-main-600 fw-semibold text-xxl">ЖУРНАЛ</span>
        </a>

        <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
            <div class="p-20 pt-10">
                <ul class="sidebar-menu">
                    @foreach ($menuItems as $item)
                        @php
                            $pattern = $item['pattern'] ?? null;
                            if ($pattern === '/') {
                                $isActive = $currentPath === '';
                            } elseif ($pattern) {
                                $isActive = request()->is($pattern);
                            } else {
                                $isActive = request()->fullUrlIs($item['url']);
                            }
                        @endphp
                        <li @class(['sidebar-menu__item', 'active' => $isActive])>
                            <a href="{{ $item['url'] }}"
                               @class(['sidebar-menu__link', 'active' => $isActive, 'sidebar-menu__link--active' => $isActive])>
                                <span class="icon"><i class="{{ $item['icon'] }}"></i></span>
                                <span class="text">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="px-20 pb-32">
                @yield('sidebar-footer')
            </div>
        </div>
    </aside>
    <!-- ============================ Sidebar End  ============================ -->

    <div class="dashboard-main-wrapper">
        <div class="top-navbar flex-between gap-16">
            <div class="flex-align gap-16">
                <button type="button" class="toggle-btn d-xl-none d-flex text-26 text-gray-500">
                    <i class="ph ph-list"></i>
                </button>

                @hasSection('topbar-left')
                    @yield('topbar-left')
                @else
                    <form action="#" class="w-350 d-sm-block d-none">
                        <div class="position-relative">
                            <button type="submit" class="input-icon text-xl d-flex text-gray-100 pointer-event-none">
                                <i class="ph ph-magnifying-glass"></i>
                            </button>
                            <input type="text" class="form-control ps-40 h-40 border-transparent focus-border-main-600 bg-main-50 rounded-pill placeholder-15" placeholder="Поиск...">
                        </div>
                    </form>
                @endif
            </div>

            <div class="flex-align gap-16">
                @hasSection('topbar-right')
                    @yield('topbar-right')
                @else
                    @php
                        $user = auth()->user();
                        $fullName = $user?->name ?? 'Александр Иванов';
                        $roleHint = $user?->email ?? 'Педагог';
                        $initials = '';
                        if ($fullName) {
                            $parts = preg_split('/\s+/u', trim($fullName));
                            $initials = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
                            if (isset($parts[1])) {
                                $initials .= mb_strtoupper(mb_substr($parts[1], 0, 1));
                            }
                        }
                        if (empty($initials)) {
                            $initials = 'E';
                        }
                    @endphp
                    <div class="flex-align gap-12">
                        <div class="d-flex flex-column text-end">
                            <span class="fw-semibold text-15 text-gray-900">{{ $fullName }}</span>
                            <span class="text-xs text-gray-200">{{ $roleHint }}</span>
                        </div>
                        <span class="w-44 h-44 rounded-circle bg-main-50 text-main-600 fw-semibold flex-center text-md">
                            {{ $initials }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div class="dashboard-main-content">
            <div class="dashboard-body">
                <div class="breadcrumb-with-buttons mb-16 flex-between flex-wrap gap-6">
                    <div class="breadcrumb mb-12">
                        @hasSection('breadcrumb')
                            @yield('breadcrumb')
                        @else
                            @php
                                $pageHeading = trim($__env->yieldContent('page_title'));
                                if ($pageHeading === '') {
                                    $pageHeading = trim($__env->yieldContent('title', 'Страница'));
                                }
                            @endphp
                            <ul class="flex-align gap-4">
                                <li><a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Главная</a></li>
                                <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                                <li><span class="text-main-600 fw-normal text-15">{{ $pageHeading }}</span></li>
                            </ul>
                        @endif
                    </div>

                    <div class="flex-align gap-8 flex-wrap">
                        @hasSection('breadcrumb-actions')
                            @yield('breadcrumb-actions')
                        @endif

                        @if (trim($__env->yieldContent('page_actions')))
                            <div class="flex-align gap-8 flex-wrap">
                                @yield('page_actions')
                            </div>
                        @endif
                    </div>
                </div>

                @hasSection('page_subtitle')
                    <p class="text-gray-300 mb-16">@yield('page_subtitle')</p>
                @endif

                <div class="dashboard-page-content">
                    @hasSection('content')
                        @yield('content')
                    @else
                        <div class="bg-white border border-dashed border-gray-100 rounded-16 p-32 text-center">
                            <p class="text-gray-200 mb-0">Добавьте содержимое в секцию <code>@section('content')</code>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @stack('modals')
    </div>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    @stack('scripts')
</body>
</html>
