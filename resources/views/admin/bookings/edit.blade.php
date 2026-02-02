@extends('layouts.admin')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Edit Booking #{{ $booking->booking_number }}</h3>
            <p class="text-muted small mb-0">Modify stay dates, guest details, or room quantities.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}" id="edit-booking-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-user small"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Guest Profile</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Existing Customer</label>
                                <select name="user_id" id="user_id" class="form-select border-light-subtle rounded-3 shadow-none bg-light">
                                    <option value="">New Guest</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('user_id', $booking->user_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none @error('guest_name') is-invalid @enderror" 
                                       name="guest_name" value="{{ old('guest_name', $booking->guest_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-light-subtle rounded-3 shadow-none @error('guest_email') is-invalid @enderror" 
                                       name="guest_email" value="{{ old('guest_email', $booking->guest_email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none" 
                                       name="guest_phone" value="{{ old('guest_phone', $booking->guest_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Address</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none" 
                                       name="guest_address" value="{{ old('guest_address', $booking->guest_address) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle text-success rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <i class="fas fa-bed small"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0">Room Selection</h5>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" id="add-room-type">
                             + Add Room
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="room-types-container">
                            @foreach(old('room_types', $booking->details) as $index => $detail)
                                <div class="room-type-item mb-3 p-3 rounded-4 bg-light border border-light-subtle">
                                    <div class="row align-items-end">
                                        <div class="col-md-7">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Room Category</label>
                                            <select name="room_types[{{ $index }}][room_type_id]" class="form-select border-0 rounded-3 shadow-sm" required>
                                                <option value="">Select Room Type</option>
                                                @foreach($roomTypes as $roomType)
                                                    <option value="{{ $roomType->id }}" 
                                                        {{ (is_object($detail) ? $detail->room_type_id : ($detail['room_type_id'] ?? '')) == $roomType->id ? 'selected' : '' }}>
                                                        {{ $roomType->name }} - {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($roomType->price_per_night, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Qty</label>
                                            <input type="number" name="room_types[{{ $index }}][quantity]" 
                                                   class="form-control border-0 rounded-3 shadow-sm text-center" 
                                                   value="{{ is_object($detail) ? $detail->quantity : ($detail['quantity'] ?? 1) }}" 
                                                   min="1" required>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button type="button" class="btn btn-action btn-delete remove-room-type w-100 rounded-3">
                                                <i class="fas fa-trash-alt me-2"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div>
                    
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4">Stay Schedule</h5>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Check-In</label>
                                <input type="date" id="check_in_date" name="check_in_date" 
                                       class="form-control border-light-subtle rounded-3 shadow-none fw-bold text-primary" 
                                       value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Check-Out</label>
                                <input type="date" id="check_out_date" name="check_out_date" 
                                       class="form-control border-light-subtle rounded-3 shadow-none fw-bold text-primary" 
                                       value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Adults</label>
                                    <input type="number" class="form-control border-light-subtle rounded-3 shadow-none" name="adults" value="{{ old('adults', $booking->adults) }}" min="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Children</label>
                                    <input type="number" class="form-control border-light-subtle rounded-3 shadow-none" name="children" value="{{ old('children', $booking->children) }}" min="0">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                                <select name="status" class="form-select border-light-subtle rounded-3 shadow-none fw-bold">
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                    <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-bold">Nights:</span>
                                <span class="h6 fw-bold mb-0 text-dark" id="total-nights-display">--</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-2">
                                <i class="fas fa-save me-2"></i> Update Booking
                            </button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Internal Notes</label>
                            <textarea class="form-control border-light-subtle bg-light rounded-3 shadow-none" 
                                      name="special_requests" rows="3" placeholder="Notes...">{{ old('special_requests', $booking->special_requests) }}</textarea>
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
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-light { background-color: #f8fafc !important; }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd !important;
        background-color: #fff !important;
    }

    .btn-delete { background-color: #fee2e2; color: #dc2626; border: none; }
    .btn-delete:hover { background-color: #dc2626; color: white; }
    
    .form-label { margin-bottom: 0.4rem; }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('check_in_date');
        const checkOutInput = document.getElementById('check_out_date');
        const nightsDisplay = document.getElementById('total-nights-display');

        function calculateNights() {
            if (!checkInInput.value || !checkOutInput.value) return;
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);

            if (checkOut > checkIn) {
                const diffTime = Math.abs(checkOut - checkIn);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                nightsDisplay.innerText = diffDays + (diffDays === 1 ? ' Night' : ' Nights');
                nightsDisplay.classList.remove('text-danger');
            } else {
                nightsDisplay.innerText = 'Invalid Dates';
                nightsDisplay.classList.add('text-danger');
            }
        }

        checkInInput.addEventListener('change', calculateNights);
        checkOutInput.addEventListener('change', calculateNights);
        calculateNights();

        // Room management
        let roomTypeIndex = {{ count(old('room_types', $booking->details)) }};
        
        document.getElementById('add-room-type').addEventListener('click', function() {
            const container = document.getElementById('room-types-container');
            const items = container.querySelectorAll('.room-type-item');
            const template = items[0].cloneNode(true);
            
            template.querySelectorAll('select, input').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, '[' + roomTypeIndex + ']');
                }
            });
            
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
                    alert('At least one room category is required.');
                }
            }
        });
    });
</script>
@endpush