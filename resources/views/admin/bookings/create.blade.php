@extends('layouts.admin')

@section('title', 'Create Booking')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Create New Booking</h3>
            <p class="text-muted small mb-0">Enter guest information and stay details to reserve a room.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.bookings.store') }}">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold text-dark mb-0">Guest Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="user_id" class="form-label small fw-bold text-muted text-uppercase">Existing Customer (Optional)</label>
                                <select name="user_id" id="user_id" class="form-select border-light-subtle rounded-3 shadow-none bg-light">
                                    <option value="">-- Select or Leave for New Guest --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_name" class="form-label small fw-bold text-muted text-uppercase">Guest Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" value="{{ old('guest_name') }}" required placeholder="e.g. John Doe">
                                @error('guest_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_email" class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-light-subtle rounded-3 shadow-none @error('guest_email') is-invalid @enderror" 
                                       id="guest_email" name="guest_email" value="{{ old('guest_email') }}" required placeholder="guest@example.com">
                                @error('guest_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_phone" class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('guest_phone') is-invalid @enderror" 
                                       id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" placeholder="+1 234 567 890">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_address" class="form-label small fw-bold text-muted text-uppercase">Address</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none" 
                                       id="guest_address" name="guest_address" value="{{ old('guest_address') }}" placeholder="Street, City, Country">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Selected Rooms</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" id="add-room-type">
                             + Add Room
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="room-types-container">
                            <div class="room-type-item mb-3 p-3 rounded-4 bg-light border border-light-subtle">
                                <div class="row align-items-end">
                                    <div class="col-md-7 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Room Type <span class="text-danger">*</span></label>
                                        <select name="room_types[0][room_type_id]" class="form-select border-0 rounded-3 shadow-sm room-type-select" required>
                                            <option value="">Select Room Type</option>
                                            @foreach($roomTypes as $roomType)
                                                <option value="{{ $roomType->id }}" data-price="{{ $roomType->price_per_night }}">
                                                    {{ $roomType->name }} - {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($roomType->price_per_night, 2) }}/night
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="room_types[0][quantity]" class="form-control border-0 rounded-3 shadow-sm" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-2 text-md-end">
                                        <button type="button" class="btn btn-action btn-delete remove-room-type">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4">Reservation Info</h5>
                        
                        <div class="mb-3">
                            <label for="check_in_date" class="form-label small fw-bold text-muted text-uppercase">Check-in Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control border-light-subtle rounded-3 shadow-none" id="check_in_date" name="check_in_date" value="{{ old('check_in_date') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="check_out_date" class="form-label small fw-bold text-muted text-uppercase">Check-out Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control border-light-subtle rounded-3 shadow-none" id="check_out_date" name="check_out_date" value="{{ old('check_out_date') }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="adults" class="form-label small fw-bold text-muted text-uppercase">Adults</label>
                                <input type="number" class="form-control border-light-subtle rounded-3 shadow-none" id="adults" name="adults" value="{{ old('adults', 1) }}" min="1">
                            </div>
                            <div class="col-6">
                                <label for="children" class="form-label small fw-bold text-muted text-uppercase">Children</label>
                                <input type="number" class="form-control border-light-subtle rounded-3 shadow-none" id="children" name="children" value="{{ old('children', 0) }}" min="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label small fw-bold text-muted text-uppercase">Booking Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select border-light-subtle rounded-3 shadow-none fw-bold text-primary" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="special_requests" class="form-label small fw-bold text-muted text-uppercase">Notes</label>
                            <textarea class="form-control border-light-subtle rounded-3 shadow-none" id="special_requests" name="special_requests" rows="3" placeholder="Dietary needs, late check-in, etc.">{{ old('special_requests') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                            Complete Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-light { background-color: #f8fafc !important; }
    
    /* Reusing your Premium Action Button Style for the "Remove" button */
    .btn-action {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-delete { background-color: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background-color: #dc2626; color: white; }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd !important;
        background-color: #fff !important;
    }

    .form-label { margin-bottom: 0.4rem; }
</style>

@push('scripts')
<script>
    let roomTypeIndex = 1;
    
    document.getElementById('add-room-type').addEventListener('click', function() {
        const container = document.getElementById('room-types-container');
        const firstItem = container.querySelector('.room-type-item');
        const template = firstItem.cloneNode(true);
        
        // Update input names for the new index
        template.querySelectorAll('select, input').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, '[' + roomTypeIndex + ']');
            }
        });
        
        // Clear values
        template.querySelector('select').selectedIndex = 0;
        template.querySelector('input[type="number"]').value = 1;
        
        container.appendChild(template);
        roomTypeIndex++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-room-type')) {
            const items = document.querySelectorAll('.room-type-item');
            if (items.length > 1) {
                e.target.closest('.room-type-item').remove();
            } else {
                alert('At least one room type is required.');
            }
        }
    });
</script>
@endpush
@endsection