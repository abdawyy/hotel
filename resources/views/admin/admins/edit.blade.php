@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}" class="text-decoration-none text-muted">Admins</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Edit Profile</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">Edit: {{ $admin->name }}</h3>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Personal Information</h5>
                        <p class="text-muted small mb-0">Basic account details and contact info.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" class="form-control rounded-3 border-light-subtle @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="phone" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="address" class="form-label small fw-bold text-muted text-uppercase">Home/Office Address</label>
                                <textarea class="form-control rounded-3 border-light-subtle @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address', $admin->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Access Control & Permissions</h5>
                                <p class="text-muted small mb-0">Define what this administrator can view or manage.</p>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-pill px-3 py-1 extra-small fw-bold border border-primary-subtle">
                                <i class="bi bi-shield-lock-fill me-1"></i> Security: 
                                @if($admin->role && $admin->role->name === 'admin') High @else Standard @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        @if(isset($canEditPermissions) && $canEditPermissions)
                            @if($admin->id == auth()->id())
                                <div class="alert bg-primary-subtle border-0 rounded-4 p-3 mb-4 d-flex align-items-center border-start border-primary border-4">
                                    <i class="bi bi-exclamation-triangle-fill text-primary fs-4 me-3"></i>
                                    <div class="small text-primary fw-bold">You are modifying your own permissions. Avoid removing access to core features.</div>
                                </div>
                            @endif

                            <div class="row g-4">
                                @foreach($permissions as $category => $categoryPermissions)
                                    <div class="col-12">
                                        <div class="permission-group border rounded-4 p-0 overflow-hidden bg-white">
                                            <div class="bg-light px-4 py-2 border-bottom d-flex align-items-center justify-content-between">
                                                <h6 class="fw-bold text-dark text-uppercase small mb-0">
                                                    <i class="bi bi-folder2-open text-primary me-2"></i> {{ ucfirst($category) }}
                                                </h6>
                                                <span class="badge bg-white text-muted border rounded-pill px-2 py-1 extra-small">{{ count($categoryPermissions) }} Actions</span>
                                            </div>
                                            
                                            <div class="p-3">
                                                <div class="row g-3">
                                                    @foreach($categoryPermissions as $permission)
                                                        <div class="col-md-6 col-xl-4">
                                                            <div class="permission-card h-100 p-3 rounded-3 border transition-all">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <label class="fw-bold text-dark small mb-0 cursor-pointer" for="p_{{ $permission->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                    <div class="form-check form-switch p-0 m-0">
                                                                        <input class="form-check-input custom-switch m-0 cursor-pointer" type="checkbox" 
                                                                               name="permissions[]" value="{{ $permission->id }}" 
                                                                               id="p_{{ $permission->id }}"
                                                                               {{ in_array($permission->id, old('permissions', $selectedPermissions)) ? 'checked' : '' }}>
                                                                    </div>
                                                                </div>
                                                                @if($permission->description)
                                                                    <p class="text-muted mb-0 extra-small lh-sm">{{ $permission->description }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                                    <i class="bi bi-lock fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Management Restricted</h6>
                                <p class="text-muted small mx-auto" style="max-width: 320px;">Your current role does not have authority to modify administrative permissions.</p>
                                
                                @if($admin->role && $admin->role->name === 'admin')
                                    <div class="d-inline-block px-4 py-2 bg-success-subtle text-success border border-success-subtle rounded-pill small fw-bold mt-2">
                                        <i class="bi bi-patch-check-fill me-1"></i> Super Admin Account (Full Access)
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Account Security</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="alert bg-light border-0 rounded-3 small text-muted mb-4">
                            <i class="bi bi-shield-check me-1"></i> Leave empty to keep the current password.
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-muted text-uppercase">New Password</label>
                            <input type="password" class="form-control rounded-3 border-light-subtle @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="••••••••">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-0">
                            <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">Confirm Password</label>
                            <input type="password" class="form-control rounded-3 border-light-subtle" 
                                   id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 2rem;">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-lg bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                            <span class="fs-2 fw-bold">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $admin->name }}</h6>
                        <p class="text-muted extra-small mb-4">Last updated: {{ $admin->updated_at->diffForHumans() }}</p>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                            
                            @php 
                                $backLink = auth()->user()->hasPermission('admins.view') ? route('admin.admins.index') : route('admin.admins.show', $admin->id);
                            @endphp
                            <a href="{{ $backLink }}" class="btn btn-light border rounded-3 py-2 text-muted fw-bold">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Global Styling Overrides */
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.72rem; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.25s ease; }

    /* Permission Component Styling */
    .permission-group { border-color: #f1f5f9 !important; }
    .permission-card { background-color: #ffffff; border-color: #f1f5f9 !important; }
    .permission-card:hover { 
        border-color: #4338ca !important; 
        background-color: #f8faff; 
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.05); 
    }

    /* Custom Modern Switch */
    .custom-switch {
        cursor: pointer;
        width: 2.4em !important;
        height: 1.2em !important;
    }
    .custom-switch:checked {
        background-color: #4338ca !important;
        border-color: #4338ca !important;
    }

    /* Form Focus Effects */
    .form-control:focus {
        border-color: #4338ca;
        box-shadow: 0 0 0 0.25rem rgba(67, 56, 202, 0.1);
    }
</style>
@endsection