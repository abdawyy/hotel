@extends('layouts.admin')

@section('title', 'Amenity Details')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.amenities.index') }}" class="text-decoration-none text-muted">Amenities</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Details</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">{{ $amenity->name }}</h3>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.amenities.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">General Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted small fw-bold text-uppercase">Amenity Name</div>
                        <div class="col-sm-8 fw-bold text-dark fs-5">{{ $amenity->name }}</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted small fw-bold text-uppercase">Description</div>
                        <div class="col-sm-8 text-secondary">
                            {{ $amenity->description ?? 'No detailed description available for this amenity.' }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted small fw-bold text-uppercase">Icon Identifier</div>
                        <div class="col-sm-8">
                            <code class="bg-light px-2 py-1 rounded text-primary">{{ $amenity->icon ?? 'none' }}</code>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-sm-4 text-muted small fw-bold text-uppercase">Created Date</div>
                        <div class="col-sm-8 text-dark">
                            <i class="bi bi-calendar3 me-2 text-muted"></i>{{ $amenity->created_at->format('F d, Y') }} 
                            <span class="text-muted ms-2 small">({{ $amenity->created_at->format('h:i A') }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Linked Room Types</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill">
                        {{ $amenity->roomTypes->count() }} Total
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($amenity->roomTypes->count() > 0)
                        <div class="row g-3">
                            @foreach($amenity->roomTypes as $roomType)
                                <div class="col-md-6">
                                    <a href="{{ route('admin.rooms.show', $roomType->id) }}" class="room-type-link d-flex align-items-center p-3 rounded-3 border text-decoration-none">
                                        <div class="avatar-xs bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                            <i class="bi bi-door-open text-primary"></i>
                                        </div>
                                        <div class="fw-bold text-dark">{{ $roomType->name }}</div>
                                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                            <p class="text-muted mb-0 small">This amenity is not yet assigned to any room types.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-5 text-center bg-primary text-white">
                    <div class="icon-display-circle bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        @if($amenity->icon)
                            <i class="{{ $amenity->icon }} fs-1"></i>
                        @else
                            <i class="bi bi-app-indicator fs-1"></i>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1">Amenity Preview</h5>
                    <p class="small opacity-75 mb-0">Visual ID: {{ $amenity->icon ?? 'Not Set' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                    <h6 class="fw-bold text-dark mb-0">Management Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        @if(auth()->user()->hasPermission('amenities.edit'))
                            <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn btn-action-edit-lg d-flex align-items-center justify-content-center py-2 rounded-3 shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> Edit Amenity
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('amenities.delete'))
                            <form action="{{ route('admin.amenities.destroy', $amenity->id) }}" method="POST" class="delete-form w-100">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-action-delete-lg w-100 d-flex align-items-center justify-content-center py-2 rounded-3 shadow-sm" data-message="Are you sure you want to delete this amenity?">
                                    <i class="bi bi-trash3 me-2"></i> Delete Amenity
                                </button>
                            </form>
                        @endif
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="text-center">
                        <small class="text-muted">Last updated: {{ $amenity->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    
    /* Room Type Link Styling */
    .room-type-link {
        transition: all 0.2s ease;
        border-color: #f1f5f9 !important;
    }
    .room-type-link:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
        transform: translateX(5px);
    }

    /* Action Buttons Sidebar */
    .btn-action-edit-lg {
        background-color: #fffbeb;
        color: #92400e;
        font-weight: 600;
        border: 1px solid #fef3c7;
        text-decoration: none;
    }
    .btn-action-edit-lg:hover {
        background-color: #f59e0b;
        color: white;
    }

    .btn-action-delete-lg {
        background-color: #fef2f2;
        color: #991b1b;
        font-weight: 600;
        border: 1px solid #fee2e2;
    }
    .btn-action-delete-lg:hover {
        background-color: #ef4444;
        color: white;
    }

    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
@endsection