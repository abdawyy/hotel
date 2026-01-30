@extends('layouts.admin')

@section('title', 'Edit Amenity')
@section('page-title', 'Edit Amenity: ' . $amenity->name)

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.amenities.update', $amenity->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $amenity->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="icon" class="form-label">Icon (e.g., bi-wifi, bi-tv)</label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                           id="icon" name="icon" value="{{ old('icon', $amenity->icon) }}" placeholder="bi-wifi">
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $amenity->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.amenities.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Amenity</button>
            </div>
        </form>
    </div>
</div>
@endsection
