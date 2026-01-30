<x-guest-layout>
    <div class="text-center mb-4">
        <div class="bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px;">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
        <p class="text-muted small">Please enter your details to access your account</p>
    </div>

    <x-auth-session-status class="mb-4 alert alert-info border-0 shadow-sm rounded-3 py-2 small" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input id="email" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('email') is-invalid @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="name@company.com"
                       required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-decoration-none small fw-bold text-primary transition-all hover-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot?') }}
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-key text-muted"></i>
                </span>
                <input id="password" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('password') is-invalid @enderror"
                       type="password" 
                       name="password" 
                       placeholder="••••••••"
                       required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input custom-checkbox cursor-pointer" name="remember">
                <label for="remember_me" class="form-check-label small text-muted cursor-pointer">
                    {{ __('Stay logged in') }}
                </label>
            </div>
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-3 fs-6 fw-bold shadow-sm py-2">
                {{ __('Sign In') }} <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </div>

        <div class="text-center">
            <p class="small text-muted mb-0">Don't have an account?</p>
            <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-primary">
                {{ __('Create a free account') }}
            </a>
        </div>
    </form>

    <style>
        /* Modern Enhancements */
        .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
        .text-primary { color: #4338ca !important; }
        .btn-primary { background-color: #4338ca; border-color: #4338ca; }
        .btn-primary:hover { background-color: #3730a3; border-color: #3730a3; }
        
        .form-control:focus {
            border-color: #4338ca;
            box-shadow: 0 0 0 0.25rem rgba(67, 56, 202, 0.1);
        }

        .input-group-text {
            color: #64748b;
        }

        .custom-checkbox:checked {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .cursor-pointer { cursor: pointer; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-underline:hover { text-decoration: underline !important; }
    </style>
</x-guest-layout>