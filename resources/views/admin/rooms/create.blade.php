@extends('layouts.admin')

@section('title', 'Create Room Type')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}" class="text-decoration-none">Rooms</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark">Create New Room Type</h3>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Basic Information</h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-semibold">Room Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" 
                                       id="name" name="name" placeholder="e.g. Deluxe Ocean Suite" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-semibold">Price Per Night <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}</span>
                                    <input type="number" step="0.01" class="form-control form-control-lg rounded-end-3 @error('price_per_night') is-invalid @enderror" 
                                           id="price_per_night" name="price_per_night" value="{{ old('price_per_night') }}" min="0" required>
                                </div>
                                @error('price_per_night')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" placeholder="Briefly describe the room features...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success-subtle text-success rounded-3 p-2 me-3">
                                <i class="fas fa-tv"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Amenities & Features</h5>
                        </div>

                        <div class="row g-2">
                            @if($amenities->count() > 0)
                                @foreach($amenities as $amenity)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="amenity-item">
                                            <input type="checkbox" class="btn-check" name="amenities[]" 
                                                   value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
                                                   {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-light text-dark w-100 text-start d-flex align-items-center rounded-3 p-3 border" for="amenity_{{ $amenity->id }}">
                                                <i class="fas {{ $amenity->icon ?? 'fa-check-circle' }} me-2 text-primary"></i>
                                                <span class="small">{{ $amenity->name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-light border text-center rounded-3">
                                        No amenities found. <a href="{{ route('admin.amenities.create') }}" class="text-primary fw-bold">Add some here</a>.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-uppercase small text-muted">Capacity & Configuration</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Max Guests</label>
                            <div class="input-group border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-users text-muted"></i></span>
                                <input type="number" class="form-control border-0 px-0" name="max_guests" value="{{ old('max_guests', 2) }}" min="1">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Max Adults</label>
                                <input type="number" class="form-control rounded-3" name="max_adults" value="{{ old('max_adults', 2) }}" min="1">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Max Children</label>
                                <input type="number" class="form-control rounded-3" name="max_children" value="{{ old('max_children', 0) }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Total Inventory (Rooms)</label>
                            <input type="number" class="form-control rounded-3" name="total_rooms" value="{{ old('total_rooms', 1) }}" min="1">
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-2" for="is_active">Available for Booking</label>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted">Room Gallery</h6>
                        <div class="upload-zone border-2 border-dashed rounded-4 p-4 text-center bg-light mb-2">
                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                            <input type="file" class="form-control form-control-sm" name="images[]" multiple accept="image/*" id="images">
                            <p class="small text-muted mt-2 mb-0">Select multiple images. The first one will be primary.</p>
                        </div>
                        @error('images.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 mb-2 fw-bold shadow-sm">
                            Create Room Type
                        </button>
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-link w-100 text-muted text-decoration-none small">
                            Cancel and go back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .rounded-4 { border-radius: 1rem !important; }
    
    /* Form Enhancements */
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
        border-color: #0d6efd;
    }

    /* Amenity Checkbox Styling */
    .btn-check:checked + .btn-outline-light {
        background-color: #eef2ff !important;
        border-color: #0d6efd !important;
        color: #0d6efd !important;
    }
    
    .btn-outline-light:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; }
    
    .upload-zone:hover {
        border-color: #0d6efd !important;
        background-color: #f0f7ff !important;
    }
</style>
@endsection