@extends('layouts.admin')

@section('title', 'Admins Management')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Admins Management</h3>
            <p class="text-muted small mb-0">Manage system access, roles, and administrative permissions.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if(auth()->user()->hasPermission('admins.create'))
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm fw-bold">
                    Create New Admin
                </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.admins.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase ms-1">Search Admin</label>
                        <div class="input-group border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase ms-1">Role Type</label>
                        <select name="role" class="form-select border-light-subtle rounded-3 shadow-none">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="custom" {{ request('role') == 'custom' ? 'selected' : '' }}>Custom Admin</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold">Filter</button>
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($admins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 border-0 py-3">Administrator</th>
                                <th class="border-0 py-3">Contact Info</th>
                                <th class="border-0 py-3 text-center">Account Role</th>
                                <th class="border-0 py-3 text-center">Permissions</th>
                                <th class="border-0 py-3">Joined Date</th>
                                <th class="pe-4 border-0 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 38px; height: 38px;">
                                                {{ substr($admin->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $admin->name }}</div>
                                                <div class="text-muted extra-small">ID: #00{{ $admin->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-500">{{ $admin->email }}</div>
                                        <div class="text-muted extra-small">{{ $admin->phone ?? 'No Phone' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($admin->role && $admin->role->name === 'admin')
                                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2" style="font-size: 0.7rem;">Super Admin</span>
                                        @else
                                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-2" style="font-size: 0.7rem;">Custom Admin</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($admin->role && $admin->role->name === 'admin')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2" style="font-size: 0.7rem;">Full Access</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                                                {{ $admin->role && $admin->role->permissions ? $admin->role->permissions->count() : 0 }} Rules
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small text-dark">{{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @php 
                                                $canView = auth()->user()->hasPermission('admins.view') || $admin->id == auth()->id();
                                                $canEdit = auth()->user()->hasPermission('admins.edit') || $admin->id == auth()->id();
                                                $canDelete = $admin->id !== auth()->id() && auth()->user()->hasPermission('admins.delete');
                                            @endphp

                                            @if($canView)
                                                <a href="{{ route('admin.admins.show', $admin->id) }}" class="btn-action btn-view" title="View Profile">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                            @endif

                                            @if($canEdit)
                                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn-action btn-edit" title="Edit Admin">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                                </a>
                                            @endif

                                            @if($canDelete)
                                                <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete" title="Remove Admin" data-message="Warning: This action will permanently remove this administrator. Continue?">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="btn-action bg-light text-muted opacity-50" title="Protected Account">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white border-top-0 py-4">
                    <div class="d-flex justify-content-center">
                        {{ $admins->links() }}
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <h5>No Administrators Found</h5>
                    <p class="small">Try adjusting your filters or search terms.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    .fw-500 { font-weight: 500; }
    
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #991b1b !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-info-subtle { background-color: #f0f9ff !important; color: #075985 !important; }
    
    .table td { padding: 1.1rem 0.5rem; }
    .table-hover tbody tr:hover { background-color: #f8fafc !important; }

    /* ACTION BUTTONS (Sync with Payments module) */
    .btn-action {
        width: 36px; height: 36px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px; border: none;
        transition: all 0.2s ease; text-decoration: none; padding: 0;
    }
    .btn-view { background-color: #e0f2fe; color: #0ea5e9; }
    .btn-view:hover { background-color: #0ea5e9; color: white; transform: translateY(-2px); }
    .btn-edit { background-color: #fef3c7; color: #d97706; }
    .btn-edit:hover { background-color: #d97706; color: white; transform: translateY(-2px); }
    .btn-delete { background-color: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background-color: #dc2626; color: white; transform: translateY(-2px); }

    .shadow-none:focus { box-shadow: none !important; border-color: #cbd5e1 !important; }
</style>
@endsection