<x-guest-layout>
    <div class="text-center mb-4">
        <div class="bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px;">
            <i class="bi bi-person-plus-fill fs-2"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Create Account</h3>
        <p class="text-muted small">Join us to manage your bookings and experience</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label small fw-bold text-muted text-uppercase">{{ __('public.name') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input id="name" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('name') is-invalid @enderror" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="John Doe"
                       required autofocus autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted text-uppercase">{{ __('public.email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input id="email" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('email') is-invalid @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="john@example.com"
                       required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label small fw-bold text-muted text-uppercase">{{ __('public.phone_number') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-telephone text-muted"></i>
                </span>
                <input id="phone" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('phone') is-invalid @enderror" 
                       type="text" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       placeholder="+1 (555) 000-0000"
                       required autocomplete="tel" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted text-uppercase">{{ __('public.password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-shield-lock text-muted"></i>
                </span>
                <input id="password" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2 @error('password') is-invalid @enderror"
                       type="password" 
                       name="password" 
                       placeholder="••••••••"
                       required autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">{{ __('public.confirm_password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-check-circle text-muted"></i>
                </span>
                <input id="password_confirmation" 
                       class="form-control border-start-0 rounded-end-3 border-light-subtle py-2"
                       type="password" 
                       name="password_confirmation" 
                       placeholder="••••••••"
                       required autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-3 fs-6 fw-bold shadow-sm py-2">
                {{ __('public.register') }} <i class="bi bi-person-check ms-1"></i>
            </button>
        </div>

        <div class="text-center">
            <p class="small text-muted mb-0">{{ __('Already have an account?') }}</p>
            <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">
                {{ __('Sign in instead') }}
            </a>
        </div>
    </form>

    <style>
        .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
        .text-primary { color: #4338ca !important; }
        .btn-primary { background-color: #4338ca; border-color: #4338ca; }
        .btn-primary:hover { background-color: #3730a3; border-color: #3730a3; }
        
        .form-control:focus {
            border-color: #4338ca;
            box-shadow: 0 0 0 0.25rem rgba(67, 56, 202, 0.1);
        }

        .input-group-text { color: #64748b; }
        
        /* Smooth scale-up for the card icon */
        .bi-person-plus-fill { transition: transform 0.3s ease; }
        .bg-primary-subtle:hover .bi-person-plus-fill { transform: scale(1.1); }
    </style>
</x-guest-layout>