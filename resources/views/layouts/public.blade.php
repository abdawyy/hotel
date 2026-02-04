<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Http\Controllers\LanguageController::isRtl(app()->getLocale()) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Hotel Reservation'))</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-navbar: #0f172a; 
            --bg-footer: #0f172a;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --logout-red: #fb7185;
            --accent-color: #3b82f6; /* Modern Blue */
        }
        
        [data-theme="dark"] {
            --bg-primary: #020617;
            --bg-secondary: #0f172a;
            --bg-navbar: #000000;
            --bg-footer: #000000;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #1e293b;
            --card-bg: #0f172a;
            --logout-red: #f43f5e;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            transition: background 0.3s ease;
        }
        
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            padding: 30px;
        }

        /* Elements Styling */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .form-control, .form-select {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Dropdown Polishing */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 16px;
            padding: 0.75rem;
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
                        <a class="nav-link px-3" href="{{ route('home') }}">{{ __('public.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('rooms.index') }}">{{ __('public.rooms') }}</a>
                    </li>

                    @auth
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle bg-white bg-opacity-10 rounded-pill px-4" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
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
                        <li class="nav-item ms-lg-3"><a class="nav-link" href="{{ route('login') }}">{{ __('public.login') }}</a></li>
                        <li class="nav-item">
                            <a class="btn btn-primary px-4" href="{{ route('register') }}">{{ __('public.register') }}</a>
                        </li>
                    @endauth

                    <div class="d-flex align-items-center ms-lg-4 gap-3 ps-lg-3 border-start border-white border-opacity-10">
                        <!-- Language Switcher -->
                        <div class="dropdown">
                            <button class="btn btn-sm text-light px-3 bg-white bg-opacity-10 rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <button class="btn btn-sm text-light px-2 bg-white bg-opacity-10 rounded-circle" id="themeToggle" type="button" style="width:35px; height:35px">
                            <i class="bi bi-sun-fill" id="themeIcon"></i>
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
                    themeIcon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
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