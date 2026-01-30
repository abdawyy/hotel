@extends('layouts.admin')

@section('title', 'Create Customer')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Create New Customer</h3>
            <p class="text-muted small mb-0">Register a new guest account for future bookings and history tracking.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.customers.store') }}">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-id-card small"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Personal Profile</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-light-subtle rounded-3 shadow-none @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="phone" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 234 567 890">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12 mb-0">
                                <label for="address" class="form-label small fw-bold text-muted text-uppercase">Physical Address</label>
                                <textarea class="form-control border-light-subtle rounded-3 shadow-none @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" placeholder="Street, City, Country">{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-info-subtle text-info rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-lock small"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Security Access</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-light-subtle rounded-3 shadow-none @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label small fw-bold text-muted text-uppercase">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control border-light-subtle rounded-3 shadow-none" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase fw-bold opacity-50 small mb-4">Customer Status</h6>
                            
                            <div class="p-3 bg-white bg-opacity-10 rounded-3 mb-4">
                                <p class="small mb-0">Creating this account will allow the customer to log in and manage their own bookings.</p>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-3">
                                <i class="fas fa-user-plus me-2"></i> Create Customer
                            </button>
                            
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-light w-100 py-2 border-0 opacity-75 small">
                                Cancel and Exit
                            </a>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-info">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-2 small text-uppercase">Note</h6>
                            <p class="text-muted small mb-0">An automated welcome email can be sent to this customer after account creation based on your notification settings.</p>
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
    }

    .form-label { margin-bottom: 0.4rem; }
    
    /* Consistency with your other styles */
    .card-header { border-bottom: none; }
</style>
@endsection