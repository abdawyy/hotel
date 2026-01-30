@extends('layouts.admin')

@section('title', 'Amenities Management')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Amenities Management</h3>
            <p class="text-muted small mb-0">Manage and track features available across your room inventory.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if(auth()->user()->hasPermission('amenities.create'))
                <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm fw-bold">
                    Add New Amenity
                </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.amenities.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase ms-1">Keyword Search</label>
                        <div class="input-group border rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search name or description..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ms-1">Usage Status</label>
                        <select name="usage" class="form-select border-light-subtle rounded-3 shadow-none">
                            <option value="">All Amenities</option>
                            <option value="used" {{ request('usage') == 'used' ? 'selected' : '' }}>Currently in Use</option>
                            <option value="unused" {{ request('usage') == 'unused' ? 'selected' : '' }}>Not Linked to Rooms</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase ms-1">Date Added</label>
                        <input type="date" name="date" class="form-control border-light-subtle rounded-3 shadow-none" value="{{ request('date') }}">
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold">Filter</button>
                            <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
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
            @if($amenities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 border-0 py-3">Amenity Name</th>
                                <th class="border-0 py-3 text-center">Icon</th>
                                <th class="border-0 py-3">Description</th>
                                <th class="border-0 py-3 text-center">Usage</th>
                                <th class="pe-4 border-0 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($amenities as $amenity)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark fs-6">{{ $amenity->name }}</div>
                                        <div class="text-muted extra-small">Created: {{ $amenity->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 38px; height: 38px;">
                                            @if($amenity->icon)
                                                <i class="{{ $amenity->icon }} text-primary fs-5"></i>
                                            @else
                                                <i class="bi bi-question-circle text-muted"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small text-wrap d-block" style="max-width: 250px;">
                                            {{ Str::limit($amenity->description ?? 'No description provided.', 60) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php $count = $amenity->roomTypes->count(); @endphp
                                        <span class="badge rounded-pill border {{ $count > 0 ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-muted border-secondary-subtle' }} px-3 py-2" style="font-weight: 600; font-size: 0.7rem;">
                                            {{ $count }} {{ Str::plural('Room Type', $count) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(auth()->user()->hasPermission('amenities.view'))
                                                <a href="{{ route('admin.amenities.show', $amenity->id) }}" class="btn-action btn-view" title="View Details">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasPermission('amenities.edit'))
                                                <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn-action btn-edit" title="Edit Amenity">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                                </a>
                                            @endif

                                            @if(auth()->user()->hasPermission('amenities.delete'))
                                                <form action="{{ route('admin.amenities.destroy', $amenity->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete" title="Delete Amenity" data-message="Permanently remove this amenity?">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    </button>
                                                </form>
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
                        {{ $amenities->links() }}
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-box-seam fs-1 opacity-25 d-block mb-3"></i>
                    <h5>No Amenities Found</h5>
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 mt-2">Clear All Filters</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #991b1b !important; }
    
    .table td { padding: 1.1rem 0.5rem; }
    .table-hover tbody tr:hover { background-color: #f8fafc !important; }

    /* ACTION BUTTONS (Matching Payments UI) */
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