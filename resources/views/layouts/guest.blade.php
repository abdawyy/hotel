<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Prevent FOUC by setting theme immediately -->
        <script>
            (function() {
                const theme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', theme);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --bg-primary: #f8f9fa;
                --bg-secondary: #ffffff;
                --bg-tertiary: #f1f5f9;
                --text-primary: #1e293b;
                --text-secondary: #64748b;
                --text-muted: #94a3b8;
                --border-color: #e2e8f0;
                --card-bg: #ffffff;
                --card-header-bg: #f8fafc;
                --input-bg: #ffffff;
                --accent-color: #3b82f6;
                --shadow-color: rgba(0, 0, 0, 0.1);
            }
            
            [data-theme="dark"] {
                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --bg-tertiary: #334155;
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --text-muted: #94a3b8;
                --border-color: #334155;
                --card-bg: #1e293b;
                --card-header-bg: #334155;
                --input-bg: #1e293b;
                --shadow-color: rgba(0, 0, 0, 0.3);
            }
            
            * {
                transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                background-color: var(--bg-primary);
                color: var(--text-primary);
            }
            
            .bg-light {
                background-color: var(--bg-primary) !important;
            }
            
            .card {
                background-color: var(--card-bg) !important;
                border: 1px solid var(--border-color) !important;
            }
            
            .card-body {
                background-color: var(--card-bg);
                color: var(--text-primary);
            }
            
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
            
            .text-muted {
                color: var(--text-muted) !important;
            }
            
            a {
                color: var(--accent-color);
            }
            
            .text-primary {
                color: var(--accent-color) !important;
            }
            
            h1, h2, h3, h4, h5, h6 {
                color: var(--text-primary);
            }
            
            /* Theme Toggle */
            .theme-toggle {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                background-color: var(--card-bg);
                border: 1px solid var(--border-color);
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 15px var(--shadow-color);
            }
            
            .theme-toggle:hover {
                background-color: var(--bg-tertiary);
            }
            
            .theme-toggle i {
                font-size: 1.2rem;
                color: var(--text-primary);
            }
        </style>
    </head>
    <body>
        <!-- Theme Toggle -->
        <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>
        
        @include('partials.flash-toaster')
        <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center py-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <h3 class="text-primary">{{ \App\Models\Setting::getValue('hotel_name', config('app.name', 'Hotel')) }}</h3>
                </a>
            </div>

            <div class="w-100" style="max-width: 450px;">
                <div class="card shadow">
                    <div class="card-body p-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Theme management
            function getTheme() {
                return localStorage.getItem('theme') || 'light';
            }
            
            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                updateThemeIcon(theme);
            }
            
            function updateThemeIcon(theme) {
                const icon = document.getElementById('themeIcon');
                if (icon) {
                    icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
                }
            }
            
            function toggleTheme() {
                const currentTheme = getTheme();
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            }
            
            // Initialize theme on page load
            document.addEventListener('DOMContentLoaded', function() {
                setTheme(getTheme());
            });
        </script>
    </body>
</html>
