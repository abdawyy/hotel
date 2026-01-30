<x-guest-layout>
    <div class="text-center mb-4">
        <div class="bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px;">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Set New Password</h3>
        <p class="text-muted small">Secure your account with a new password</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                       value="{{ old('email', $request->email) }}" 
                       required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted text-uppercase">New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3">
                    <i class="bi bi-key text-muted"></i>
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
            <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">Confirm New Password</label>
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

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg rounded-3 fs-6 fw-bold shadow-sm py-2">
                {{ __('Reset Password') }} <i class="bi bi-arrow-right ms-1"></i>
            </button>
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
    </style>
</x-guest-layout>