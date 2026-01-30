@extends('layouts.admin')

@section('title', 'Create Admin')
@section('page-title', 'Create New Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <form method="POST" action="{{ route('admin.admins.store') }}">
            @csrf
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-user-shield me-2 text-primary"></i>Admin Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                            </div>
                            @error('name') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
                            </div>
                            @error('email') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                            </div>
                            @error('password') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 234 567 890">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-bold">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2" placeholder="Street, City, Country...">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-key me-2 text-warning"></i>Role Permissions</h5>
                    <span class="badge bg-info text-dark">Default: Super Admin if none selected</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Grant specific access levels by toggling the categories below.</p>
                    
                    <div class="row">
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="border rounded p-3 bg-white h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-primary mb-0">{{ ucfirst($category) }}</h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input select-all-category" type="checkbox" role="switch">
                                        </div>
                                    </div>
                                    
                                    @foreach($categoryPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox" type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                                @if($permission->description)
                                                    <i class="fas fa-info-circle ms-1 text-muted" data-bs-toggle="tooltip" title="{{ $permission->description }}"></i>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-1"></i> Create Admin account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Logic for "Select All" within a category
    document.querySelectorAll('.select-all-category').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const container = this.closest('.border');
            const checkboxes = container.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    // Initialize tooltips if using Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection