@extends('layouts.admin')

@section('title', 'Edit Room Type')
@section('page-title', 'Edit Room Type: ' . $roomType->name)

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.rooms.update', $roomType->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Basic Information</h5>
                    <hr>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Room Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $roomType->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="price_per_night" class="form-label">Price Per Night <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('price_per_night') is-invalid @enderror" 
                           id="price_per_night" name="price_per_night" value="{{ old('price_per_night', $roomType->price_per_night) }}" min="0" required>
                    @error('price_per_night')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $roomType->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Capacity</h5>
                    <hr>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_guests" class="form-label">Max Guests <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_guests') is-invalid @enderror" 
                           id="max_guests" name="max_guests" value="{{ old('max_guests', $roomType->max_guests) }}" min="1" required>
                    @error('max_guests')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_adults" class="form-label">Max Adults <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_adults') is-invalid @enderror" 
                           id="max_adults" name="max_adults" value="{{ old('max_adults', $roomType->max_adults) }}" min="1" required>
                    @error('max_adults')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="max_children" class="form-label">Max Children</label>
                    <input type="number" class="form-control @error('max_children') is-invalid @enderror" 
                           id="max_children" name="max_children" value="{{ old('max_children', $roomType->max_children) }}" min="0">
                    @error('max_children')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Room Configuration</h5>
                    <hr>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_rooms" class="form-label">Total Rooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('total_rooms') is-invalid @enderror" 
                           id="total_rooms" name="total_rooms" value="{{ old('total_rooms', $roomType->total_rooms) }}" min="1" required>
                    @error('total_rooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $roomType->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (Available for booking)
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Amenities</h5>
                    <hr>
                </div>
                <div class="col-md-12 mb-3">
                    @if($amenities->count() > 0)
                        <div class="row">
                            @foreach($amenities as $amenity)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="amenities[]" 
                                               value="{{ $amenity->id }}" 
                                               id="amenity_{{ $amenity->id }}"
                                               {{ in_array($amenity->id, old('amenities', $selectedAmenities)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                            @if($amenity->icon)
                                                <i class="bi {{ $amenity->icon }}"></i>
                                            @endif
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No amenities available. <a href="{{ route('admin.amenities.create') }}">Create amenities first</a>.</p>
                    @endif
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5>Current Images</h5>
                    <hr>
                </div>
                @if($roomType->images->count() > 0)
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            @foreach($roomType->images as $image)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="Room Image" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="delete_images[]" 
                                                       value="{{ $image->id }}" 
                                                       id="delete_image_{{ $image->id }}">
                                                <label class="form-check-label" for="delete_image_{{ $image->id }}">
                                                    Delete
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="col-md-12 mb-3">
                    <label for="images" class="form-label">Add New Images</label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                           id="images" name="images[]" multiple accept="image/*">
                    <small class="form-text text-muted">You can select multiple images to add.</small>
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Room Type</button>
            </div>
        </form>
    </div>
</div>
@endsection
