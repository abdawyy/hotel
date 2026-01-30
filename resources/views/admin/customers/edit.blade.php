@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Edit Customer: {{ $customer->name }}</h3>
            <p class="text-muted small mb-0">Update personal details, contact information, and preferences.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <i class="fas fa-times me-1 small"></i> Cancel
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-user-edit small"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Personal Profile</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-light-subtle rounded-3 shadow-none @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="phone" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-0">
                                <label for="address" class="form-label small fw-bold text-muted text-uppercase">Physical Address</label>
                                <textarea class="form-control border-light-subtle rounded-3 shadow-none @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-info-subtle text-info rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-shield-alt small"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Account Security</h6>
                                <p class="text-muted small mb-0">Password changes are handled via the dedicated security reset portal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="bg-white bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-save fa-lg text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Save Changes</h5>
                            <p class="text-muted small mb-4">Review all details carefully. Updates take effect immediately across all bookings.</p>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-3">
                                <i class="fas fa-check me-2"></i> Update Customer
                            </button>
                            
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-light w-100 py-2 border-0 opacity-75 small">
                                View Current Profile
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-2 small text-uppercase">Activity Info</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Registered:</span>
                                <span class="small fw-bold">{{ $customer->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Last Update:</span>
                                <span class="small fw-bold">{{ $customer->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-light { background-color: #f8fafc !important; }
    
    .form-control:focus {
        border-color: #0d6efd !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
    }

    .form-label { margin-bottom: 0.4rem; }
    .card-header { border-bottom: none; }
</style>
@endsection