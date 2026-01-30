@extends('layouts.admin')

@section('title', 'Admin Details')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}" class="text-decoration-none text-muted">Admins</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Admin Profile</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">{{ $admin->name }}</h3>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Admin Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted text-uppercase d-block mb-1">Full Name</label>
                            <p class="text-dark fw-bold mb-0 fs-5">{{ $admin->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted text-uppercase d-block mb-1">Email Address</label>
                            <p class="text-dark mb-0">{{ $admin->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted text-uppercase d-block mb-1">Phone Number</label>
                            <p class="text-dark mb-0">{{ $admin->phone ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted text-uppercase d-block mb-1">Account Role</label>
                            <div>
                                @if($admin->role && $admin->role->name === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-shield-check me-1"></i> Super Admin
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-person-badge me-1"></i> Custom Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted text-uppercase d-block mb-1">Address</label>
                            <p class="text-dark mb-0">{{ $admin->address ?? 'No address provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Assigned Permissions</h5>
                    <p class="text-muted small mb-0">Overview of this administrator's access rights.</p>
                </div>
                <div class="card-body p-4">
                    @if($admin->role && $admin->role->name === 'admin')
                        <div class="bg-success-subtle text-success rounded-4 p-4 text-center border border-success-subtle">
                            <i class="bi bi-patch-check-fill fs-1 mb-2 d-block"></i>
                            <h6 class="fw-bold mb-1">Super Admin Access</h6>
                            <p class="small mb-0">This account has full unrestricted access to all system modules.</p>
                        </div>
                    @else
                        @php $hasAny = false; @endphp
                        <div class="row g-4">
                            @foreach($allPermissions as $category => $categoryPermissions)
                                @php
                                    $categoryPerms = $categoryPermissions->filter(function($p) use ($adminPermissions) {
                                        return in_array($p->id, $adminPermissions);
                                    });
                                @endphp
                                @if($categoryPerms->count() > 0)
                                    @php $hasAny = true; @endphp
                                    <div class="col-12">
                                        <div class="permission-group border rounded-4 bg-white overflow-hidden">
                                            <div class="bg-light px-4 py-2 border-bottom fw-bold text-dark text-uppercase small">
                                                <i class="bi bi-folder2 text-primary me-2"></i> {{ ucfirst($category) }}
                                            </div>
                                            <div class="p-3">
                                                <div class="row g-2">
                                                    @foreach($categoryPerms as $permission)
                                                        <div class="col-md-6 col-xl-4">
                                                            <div class="bg-white border rounded-3 p-2 d-flex align-items-center">
                                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                                <span class="small fw-bold text-dark">{{ $permission->name }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        @if(!$hasAny)
                            <div class="text-center py-5 border rounded-4 bg-light">
                                <i class="bi bi-shield-slash text-muted fs-1 mb-2"></i>
                                <p class="text-muted small">No specific permissions assigned to this role.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 2rem;">
                <div class="card-body p-4 text-center">
                    <div class="avatar-lg bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <span class="fs-1 fw-bold">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ $admin->name }}</h5>
                    <p class="text-muted extra-small mb-4">Member Since: {{ $admin->created_at->format('M d, Y') }}</p>
                    
                    <hr class="my-4 opacity-10">

                    <div class="d-grid gap-2">
                        @if(auth()->user()->hasPermission('admins.edit') || $admin->id == auth()->id())
                            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-warning border-0 rounded-3 py-2 fw-bold shadow-sm text-dark">
                                <i class="bi bi-pencil-square me-1"></i> Edit Admin Profile
                            </a>
                        @endif

                        @if($admin->id !== auth()->id() && auth()->user()->hasPermission('admins.delete'))
                            <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-bold" data-message="Are you sure you want to permanently delete this administrator?">
                                    <i class="bi bi-trash me-1"></i> Delete Account
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 px-4 rounded-bottom-4">
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Last Active</span>
                        <span class="text-dark fw-bold">Online</span> </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.75rem; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #dc2626 !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    
    .permission-group { border-color: #f1f5f9 !important; }
    .avatar-lg { text-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    
    .btn-warning { background-color: #ffb020 !important; color: #000; }
    .btn-warning:hover { background-color: #f5a600 !important; }
</style>
@endsection