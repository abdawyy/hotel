<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Http\Controllers\LanguageController::isRtl(app()->getLocale()) ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard')) - {{ config('app.name', 'Hotel Reservation') }}</title>
    
    <!-- Prevent FOUC by setting theme immediately -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --bg-sidebar: #0f172a;
            --text-sidebar: #94a3b8;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --card-header-bg: #f8fafc;
            --input-bg: #ffffff;
            --accent-color: #3b82f6;
            --logout-red: #fb7185;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --table-stripe-bg: #f8fafc;
            --dropdown-bg: #ffffff;
            --modal-bg: #ffffff;
        }
        
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-sidebar: #020617;
            --text-sidebar: #64748b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-bg: #1e293b;
            --card-header-bg: #334155;
            --input-bg: #1e293b;
            --logout-red: #f43f5e;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --table-stripe-bg: #334155;
            --dropdown-bg: #1e293b;
            --modal-bg: #1e293b;
        }
        
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        
        /* Text Colors */
        .text-muted { color: var(--text-muted) !important; }
        .text-secondary { color: var(--text-secondary) !important; }
        h1, h2, h3, h4, h5, h6 { color: var(--text-primary); }
        p, span, label, small { color: inherit; }
        
        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background-color: var(--bg-sidebar);
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link {
            color: var(--text-sidebar);
            padding: 0.75rem 1.25rem;
            margin: 0.2rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .sidebar .nav-link i { font-size: 1.1rem; }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar .nav-link.active {
            color: #fff !important;
            background-color: var(--accent-color);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Fixed Logout Style */
        .logout-btn {
            color: var(--logout-red) !important;
        }

        .logout-btn:hover {
            background-color: rgba(251, 113, 133, 0.1) !important;
            color: #fff !important;
        }

        hr.sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 1.5rem 1rem;
        }
        
        /* Cards */
        .card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary);
        }
        
        .card-body {
            background-color: var(--card-bg);
            color: var(--text-primary);
        }
        
        .card-header {
            background-color: var(--card-header-bg) !important;
            border-bottom: 1px solid var(--border-color) !important;
            color: var(--text-primary);
        }
        
        .card-footer {
            background-color: var(--card-header-bg) !important;
            border-top: 1px solid var(--border-color) !important;
        }
        
        .card-title, .card-text {
            color: var(--text-primary);
        }
        
        /* Form Elements */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg) !important;
            border-color: var(--accent-color) !important;
            color: var(--text-primary) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        
        .form-control::placeholder {
            color: var(--text-muted) !important;
        }
        
        .form-label {
            color: var(--text-primary);
        }
        
        .form-text {
            color: var(--text-muted) !important;
        }
        
        .input-group-text {
            background-color: var(--bg-tertiary) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-secondary) !important;
        }
        
        /* Buttons */
        .btn-light {
            background-color: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        .btn-light:hover {
            background-color: var(--bg-tertiary) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        .btn-white {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-secondary);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        /* Dropdown */
        .dropdown-menu {
            background-color: var(--dropdown-bg) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 40px var(--shadow-color);
        }
        
        .dropdown-item {
            color: var(--text-primary) !important;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
        }
        
        .dropdown-item.active {
            background-color: var(--accent-color) !important;
            color: #fff !important;
        }
        
        .dropdown-divider {
            border-color: var(--border-color);
        }
        
        /* Tables */
        .table {
            color: var(--text-primary);
            --bs-table-bg: var(--card-bg);
            --bs-table-striped-bg: var(--table-stripe-bg);
            --bs-table-hover-bg: var(--bg-tertiary);
            --bs-table-border-color: var(--border-color);
        }
        
        .table th {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }
        
        .table td {
            border-color: var(--border-color) !important;
            color: var(--text-primary);
            background-color: var(--card-bg);
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: var(--table-stripe-bg) !important;
            color: var(--text-primary);
        }
        
        .table-hover > tbody > tr:hover > * {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-primary);
        }
        
        /* Alerts */
        .alert-light {
            background-color: var(--bg-secondary) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        .alert-info {
            background-color: rgba(6, 182, 212, 0.1) !important;
            border-color: rgba(6, 182, 212, 0.3) !important;
            color: var(--text-primary) !important;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: var(--text-primary) !important;
        }
        
        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.3) !important;
            color: var(--text-primary) !important;
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
            color: var(--text-primary) !important;
        }
        
        /* Modals */
        .modal-content {
            background-color: var(--modal-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary);
        }
        
        .modal-header {
            border-bottom-color: var(--border-color) !important;
        }
        
        .modal-footer {
            border-top-color: var(--border-color) !important;
        }
        
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* List Groups */
        .list-group-item {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        
        /* Badges */
        .badge.bg-light {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
        }
        
        /* Pagination */
        .page-link {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .page-link:hover {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .page-item.active .page-link {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .page-item.disabled .page-link {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-muted);
        }
        
        /* HR */
        hr {
            border-color: var(--border-color);
            opacity: 0.5;
        }
        
        /* Background Utilities */
        .bg-light {
            background-color: var(--bg-secondary) !important;
        }
        
        .bg-white {
            background-color: var(--card-bg) !important;
        }
        
        .border {
            border-color: var(--border-color) !important;
        }
        
        .border-light {
            border-color: var(--border-color) !important;
        }
        
        .border-light-subtle {
            border-color: var(--border-color) !important;
        }
        
        /* Nav Tabs and Pills */
        .nav-tabs {
            border-bottom-color: var(--border-color);
        }
        
        .nav-tabs .nav-link {
            color: var(--text-secondary);
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--card-bg);
            border-color: var(--border-color) var(--border-color) var(--card-bg);
            color: var(--text-primary);
        }
        
        .nav-pills .nav-link {
            color: var(--text-secondary);
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--accent-color);
        }
        
        /* Progress bars */
        .progress {
            background-color: var(--bg-tertiary);
        }
        
        /* Override text-dark for dark mode compatibility */
        .text-dark {
            color: var(--text-primary) !important;
        }
        
        /* Additional Bootstrap utility overrides */
        .text-body {
            color: var(--text-primary) !important;
        }
        
        .link-dark {
            color: var(--text-primary) !important;
        }
        
        .link-dark:hover {
            color: var(--accent-color) !important;
        }
        
        /* Stat cards */
        .bg-primary-subtle {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        
        .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }
        
        .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.1) !important;
        }
        
        .bg-danger-subtle {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }
        
        .bg-info-subtle {
            background-color: rgba(6, 182, 212, 0.1) !important;
        }
        
        /* Shadow improvements for dark mode */
        .shadow, .shadow-sm, .shadow-lg {
            box-shadow: 0 0.125rem 0.25rem var(--shadow-color) !important;
        }
        
        .shadow-lg {
            box-shadow: 0 1rem 3rem var(--shadow-color) !important;
        }

        /* Top Navbar */
        .navbar {
            background-color: var(--bg-secondary) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
        }
        
        [dir="rtl"] .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
        [dir="rtl"] .sidebar .nav-link i { transform: scaleX(-1); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0 d-none d-md-flex">
                <div class="sidebar-brand">
                    <i class="bi bi-building-fill text-primary"></i>
                    <span>{{ config('app.name', 'Hotel') }}</span>
                </div>

                <nav class="nav flex-column h-100 pb-4">
                    @php
                        $user = auth()->user();
                        if ($user && !$user->relationLoaded('role')) { $user->load('role'); }
                    @endphp

                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> {{ __('admin.dashboard') }}
                    </a>

                    @if($user && $user->hasPermission('rooms.view'))
                        <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" href="{{ route('admin.rooms.index') }}">
                            <i class="bi bi-door-open-fill"></i> {{ __('admin.rooms') }}
                        </a>
                    @endif

                    @if($user && $user->hasPermission('bookings.view'))
                        <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                            <i class="bi bi-calendar-event-fill"></i> {{ __('admin.bookings') }}
                        </a>
                    @endif

                    @if($user && $user->hasPermission('customers.view'))
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                            <i class="bi bi-people-fill"></i> {{ __('admin.customers') }}
                        </a>
                    @endif

                    @if($user && $user->hasPermission('payments.view'))
                        <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
                            <i class="bi bi-credit-card-fill"></i> {{ __('admin.payments') }}
                        </a>
                    @endif

                    @if($user && $user->hasPermission('amenities.view'))
                        <a class="nav-link {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}" href="{{ route('admin.amenities.index') }}">
                            <i class="bi bi-patch-check-fill"></i> {{ __('admin.amenities') }}
                        </a>
                    @endif

                    <hr class="sidebar-divider">

                    @if($user && $user->hasPermission('admins.view'))
                        <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
                            <i class="bi bi-shield-lock-fill"></i> {{ __('admin.admins') }}
                        </a>
                    @elseif($user)
                        <a class="nav-link {{ request()->routeIs('admin.admins.show') && request()->route('id') == auth()->id() ? 'active' : '' }}" href="{{ route('admin.admins.show', auth()->id()) }}">
                            <i class="bi bi-person-circle"></i> {{ __('admin.my_profile') }}
                        </a>
                    @endif

                    @if($user && $user->hasPermission('settings.view'))
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-gear-fill"></i> {{ __('admin.settings') }}
                        </a>
                    @endif

                    <div class="mt-auto">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-arrow-left-square"></i> {{ __('public.home') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link logout-btn border-0 w-100 bg-transparent text-start">
                                <i class="bi bi-power"></i> {{ __('public.logout') }}
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
            
            <div class="col-md-10 p-0">
                <nav class="navbar navbar-expand navbar-light">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1 fw-bold fs-5">@yield('page-title', __('admin.dashboard'))</span>
                        
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <button class="btn btn-sm border d-flex align-items-center justify-content-center" id="themeToggle" type="button" style="width:38px; height:38px; background-color: var(--bg-tertiary); border-color: var(--border-color);" title="Toggle dark mode">
                                <i class="bi bi-moon-fill" id="themeIcon" style="color: var(--text-primary);"></i>
                            </button>

                            <div class="dropdown">
                                @php
                                    $languages = \App\Http\Controllers\LanguageController::getAvailableLanguages();
                                    $currentLang = $languages[app()->getLocale()] ?? $languages['en'];
                                @endphp
                                <button class="btn btn-sm btn-white border dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                    {{ $currentLang['flag'] }} {{ strtoupper(app()->getLocale()) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3" style="max-height: 350px; overflow-y: auto;">
                                    @foreach($languages as $code => $lang)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() == $code ? 'active' : '' }}" 
                                               href="{{ route('language.switch', $code) }}">
                                                <span class="me-2">{{ $lang['flag'] }}</span>
                                                <span>{{ $lang['native'] }}</span>
                                                @if(app()->getLocale() == $code)
                                                    <i class="bi bi-check2 ms-auto"></i>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="vr mx-1 opacity-25"></div>
                            <span class="small fw-bold text-muted d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </nav>
                
                @include('partials.flash-toaster')

                <div class="p-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Logic
        const html = document.documentElement;
        const themeBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        
        const updateIcon = (theme) => {
            themeIcon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        };

        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        themeBtn.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', target);
            localStorage.setItem('theme', target);
            updateIcon(target);
        });
    </script>
    @stack('scripts')
</body>
</html>