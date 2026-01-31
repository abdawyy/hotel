<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard')) - {{ config('app.name', 'Hotel Reservation') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --bg-sidebar: #0f172a; /* Dark Navy */
            --text-sidebar: #94a3b8;
            --text-primary: #1e293b;
            --border-color: #e2e8f0;
            --accent-color: #3b82f6;
            --logout-red: #fb7185;
        }
        
        [data-theme="dark"] {
            --bg-primary: #020617;
            --bg-secondary: #0f172a;
            --bg-sidebar: #000000;
            --text-sidebar: #64748b;
            --text-primary: #f1f5f9;
            --border-color: #1e293b;
            --logout-red: #f43f5e;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            transition: all 0.3s ease;
        }
        
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
                <nav class="navbar navbar-expand navbar-light sticky-top">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1 fw-bold fs-5">@yield('page-title', __('admin.dashboard'))</span>
                        
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <button class="btn btn-sm btn-light border p-2" id="themeToggle" type="button">
                                <i class="bi bi-sun-fill" id="themeIcon"></i>
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-sm btn-white border dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                    {{ strtoupper(app()->getLocale()) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3">
                                    <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a></li>
                                    <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
                                </ul>
                            </div>

                            <div class="vr mx-1 opacity-25"></div>
                            <span class="small fw-bold text-muted d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
            themeIcon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
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