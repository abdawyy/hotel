<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Http\Controllers\LanguageController::isRtl(app()->getLocale()) ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Hotel Reservation'))</title>
    
    <!-- Prevent FOUC by setting theme immediately -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-navbar: #0f172a; 
            --bg-footer: #0f172a;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --card-header-bg: #f8fafc;
            --input-bg: #ffffff;
            --logout-red: #fb7185;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --table-stripe-bg: #f8fafc;
            --dropdown-bg: #ffffff;
            --modal-bg: #ffffff;
            --alert-bg: #f8fafc;
        }
        
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-navbar: #020617;
            --bg-footer: #020617;
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
            --alert-bg: #1e293b;
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
        p, span, label, small { color: inherit; }
        
        /* Navbar Upgrade */
        .navbar {
            background-color: var(--bg-navbar) !important;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .navbar-brand { 
            font-weight: 800; 
            letter-spacing: -1px; 
            font-size: 1.4rem;
        }

        .nav-link {
            font-weight: 500;
            transition: opacity 0.2s;
        }

        /* Hero Modern Design */
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), 
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=2070');
            background-size: cover;
            background-position: center;
            border-radius: 0 0 40px 40px;
            color: white;
            text-align: center;
        }

        .search-box-container {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px var(--shadow-color);
            padding: 30px;
        }

        /* Card Styling */
        .card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
            color: var(--text-primary);
        }
        
        .card:hover {
            transform: translateY(-5px);
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
            padding: 0.75rem 1rem;
            border-radius: 12px;
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
            background-color: var(--bg-secondary) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-secondary) !important;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
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
        
        .btn-light {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .btn-light:hover {
            background-color: var(--bg-tertiary);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        /* Dropdown */
        .dropdown-menu {
            background-color: var(--dropdown-bg) !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 40px var(--shadow-color);
            border-radius: 16px;
            padding: 0.75rem;
        }
        
        .dropdown-item {
            color: var(--text-primary) !important;
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: var(--bg-secondary) !important;
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
            --bs-table-hover-bg: var(--bg-secondary);
            --bs-table-border-color: var(--border-color);
        }
        
        .table th {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border-color: var(--border-color) !important;
        }
        
        .table td {
            border-color: var(--border-color) !important;
            color: var(--text-primary);
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: var(--table-stripe-bg);
            color: var(--text-primary);
        }
        
        /* Alerts */
        .alert {
            border-radius: 12px;
        }
        
        .alert-light {
            background-color: var(--alert-bg) !important;
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
        
        .btn-close {
            filter: var(--bs-btn-close-white-filter);
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
            background-color: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
        }
        
        .breadcrumb-item a {
            color: var(--accent-color);
        }
        
        .breadcrumb-item.active {
            color: var(--text-muted);
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
        
        /* Override text-dark for dark mode compatibility */
        .text-dark {
            color: var(--text-primary) !important;
        }
        
        .text-body {
            color: var(--text-primary) !important;
        }
        
        .link-dark {
            color: var(--text-primary) !important;
        }
        
        .link-dark:hover {
            color: var(--accent-color) !important;
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
        
        /* Accordion */
        .accordion-item {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }
        
        .accordion-button {
            background-color: var(--card-bg);
            color: var(--text-primary);
        }
        
        .accordion-button:not(.collapsed) {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        .accordion-body {
            background-color: var(--card-bg);
        }
        
        /* Toast */
        .toast {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .toast-header {
            background-color: var(--card-header-bg);
            border-bottom-color: var(--border-color);
            color: var(--text-primary);
        }

        /* Footer Polishing */
        footer {
            background-color: var(--bg-footer) !important;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        [dir="rtl"] .ms-auto { margin-left: 0 !important; margin-right: auto !important; }
    </style>
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-stars text-primary"></i> 
                {{ \App\Models\Setting::getValue('hotel_name', 'Grand Hotel') }}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-2"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="{{ route('home') }}">{{ __('public.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="{{ route('rooms.index') }}">{{ __('public.rooms') }}</a>
                    </li>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-semibold px-3" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-lock me-1"></i>{{ __('public.admin_panel') ?? 'Admin' }}
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle text-white  bg-opacity-10 rounded-pill px-4" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2 animate-slide-in">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> {{ __('public.my_bookings') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> {{ __('public.profile') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('public.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3"><a class="nav-link text-white" href="{{ route('login') }}">{{ __('public.login') }}</a></li>
                        <li class="nav-item">
                            <a class="btn btn-primary px-4" href="{{ route('register') }}">{{ __('public.register') }}</a>
                        </li>
                    @endauth

                    <div class="d-flex align-items-center ms-lg-4 gap-3 ps-lg-3 border-start border-white border-opacity-10">
                        <!-- Language Switcher -->
                        <div class="dropdown">
                            <button class="btn btn-sm text-white px-3  bg-opacity-10 rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-globe2 me-1"></i> 
                                @php
                                    $languages = \App\Http\Controllers\LanguageController::getAvailableLanguages();
                                    $currentLang = $languages[app()->getLocale()] ?? $languages['en'];
                                @endphp
                                {{ $currentLang['flag'] }} {{ strtoupper(app()->getLocale()) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow" style="max-height: 400px; overflow-y: auto;">
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
                        <!-- Theme Toggle -->
                        <button class="btn btn-sm text-white px-2 bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" id="themeToggle" type="button" style="width:38px; height:38px" title="Toggle dark mode">
                            <i class="bi bi-moon-fill" id="themeIcon"></i>
                        </button>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    @if(Route::currentRouteName() == 'home')
    <header class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Find Your Perfect Room</h1>
            <p class="lead opacity-75 mb-5">Experience luxury and comfort in the heart of the city</p>
        </div>
    </header>

    <div class="container search-box-container">
        <div class="glass-card">
            <form action="{{ route('rooms.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary">Check In</label>
                    <input type="date" class="form-control" name="check_in">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary">Check Out</label>
                    <input type="date" class="form-control" name="check_out">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Guests</label>
                    <select class="form-select">
                        <option>1 Guest</option>
                        <option>2 Guests</option>
                        <option>3+ Guests</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100 py-3 shadow-sm">
                        <i class="bi bi-search me-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @include('partials.flash-toaster')

    <main class="py-5" style="min-height: 60vh;">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="text-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                         <i class="bi bi-stars text-primary"></i> 
                         {{ \App\Models\Setting::getValue('hotel_name', 'Grand Hotel') }}
                    </h5>
                    <p class="text-secondary small">{{ \App\Models\Setting::getValue('contact_address', '123 Hotel Street, City') }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-4">Contact Info</h6>
                    <p class="mb-2 text-secondary small"><i class="bi bi-envelope me-2"></i> {{ \App\Models\Setting::getValue('contact_email', 'info@hotel.com') }}</p>
                    <p class="text-secondary small"><i class="bi bi-telephone me-2"></i> {{ \App\Models\Setting::getValue('contact_phone', '+1 234 567 890') }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-4">Stay Connected</h6>
                    <div class="d-flex gap-3 fs-5 text-secondary">
                        <i class="bi bi-facebook"></i>
                        <i class="bi bi-instagram"></i>
                        <i class="bi bi-twitter-x"></i>
                    </div>
                </div>
            </div>
            <hr class="my-5 border-secondary opacity-10">
            <div class="text-center text-secondary small">
                <p class="mb-0">&copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('hotel_name', 'Grand Hotel') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Existing Theme Management
        (function() {
            const html = document.documentElement;
            const savedTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', savedTheme);
            
            document.addEventListener('DOMContentLoaded', function() {
                const themeBtn = document.getElementById('themeToggle');
                const themeIcon = document.getElementById('themeIcon');
                
                const updateUI = (theme) => {
                    themeIcon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
                };
                
                updateUI(savedTheme);

                themeBtn.addEventListener('click', () => {
                    const current = html.getAttribute('data-theme');
                    const target = current === 'dark' ? 'light' : 'dark';
                    html.setAttribute('data-theme', target);
                    localStorage.setItem('theme', target);
                    updateUI(target);
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>