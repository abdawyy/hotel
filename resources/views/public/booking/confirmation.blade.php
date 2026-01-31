@extends('layouts.public')

@section('title', 'Booking Confirmation - ' . config('app.name'))

@section('content')
<div class="container my-5">


    
    <!-- Confirmation Header -->
    <div class="text-center mb-5">
        <div class="mb-3">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
        </div>
        <h1 class="display-4">Booking Confirmed!</h1>
        <p class="lead text-muted">Thank you for your booking. We've sent a confirmation email to <strong>{{ $booking->guest_email }}</strong></p>
        <p class="text-muted">Booking Number: <strong class="text-primary">{{ $booking->booking_number }}</strong></p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Booking Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Check-in</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-calendar-event"></i> 
                                <strong>{{ $booking->check_in_date->format('l, F d, Y') }}</strong>
                            </p>
                            <p class="text-muted small">After {{ \App\Models\Setting::getValue('check_in_time', '14:00') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Check-out</h6>
                            <p class="fs-5 mb-0">
                                <i class="bi bi-calendar-event"></i> 
                                <strong>{{ $booking->check_out_date->format('l, F d, Y') }}</strong>
                            </p>
                            <p class="text-muted small">Before {{ \App\Models\Setting::getValue('check_out_time', '12:00') }}</p>
                        </div>
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Duration</h6>
                            <p class="mb-0"><strong>{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} night(s)</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Guests</h6>
                            <p class="mb-0">
                                <strong>{{ $booking->adults }} Adult(s)</strong>
                                @if($booking->children > 0)
                                    , <strong>{{ $booking->children }} Child(ren)</strong>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($booking->special_requests)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Special Requests</h6>
                        <p class="mb-0">{{ $booking->special_requests }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Room Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> Room Details</h5>
                </div>
                <div class="card-body">
                    @foreach($booking->details as $detail)
                        <div class="row mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <div class="col-md-3">
                                @php
                                    $displayImage = $detail->roomType->primaryImage ?? $detail->roomType->images->first();
                                @endphp
                                @if($displayImage)
                                    <img src="{{ asset('storage/' . $displayImage->image_path) }}" 
                                         class="img-fluid rounded" 
                                         alt="{{ $detail->roomType->name }}"
                                         style="height: 150px; width: 100%; object-fit: cover;">
                                @else
                                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 150px;">
                                        <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h5>{{ $detail->roomType->name }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($detail->roomType->description, 150) }}</p>
                                
                                @if($detail->roomType->amenities->count() > 0)
                                    <div class="mb-2">
                                        @foreach($detail->roomType->amenities->take(5) as $amenity)
                                            <span class="badge bg-light text-dark me-1">
                                                @if($amenity->icon)
                                                    <i class="bi {{ $amenity->icon }}"></i>
                                                @endif
                                                {{ $amenity->name }}
                                            </span>
                                        @endforeach
                                        @if($detail->roomType->amenities->count() > 5)
                                            <span class="text-muted small">+{{ $detail->roomType->amenities->count() - 5 }} more</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <small class="text-muted">Quantity:</small>
                                        <p class="mb-0"><strong>{{ $detail->quantity }}x</strong></p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Price per night:</small>
                                        <p class="mb-0"><strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($detail->price_per_night, 2) }}</strong></p>
                                    </div>
                                    @if($detail->room)
                                    <div class="col-md-4">
                                        <small class="text-muted">Room Number:</small>
                                        <p class="mb-0"><span class="badge bg-info">{{ $detail->room->room_number }}</span></p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Guest Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Guest Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Name</h6>
                            <p class="mb-0"><strong>{{ $booking->guest_name }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Email</h6>
                            <p class="mb-0">{{ $booking->guest_email }}</p>
                        </div>
                        @if($booking->guest_phone)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Phone</h6>
                            <p class="mb-0">{{ $booking->guest_phone }}</p>
                        </div>
                        @endif
                        @if($booking->guest_address)
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">Address</h6>
                            <p class="mb-0">{{ $booking->guest_address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Price Summary -->
            <div class="card mb-4" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Price Summary</h5>
                </div>
                <div class="card-body">
                    @foreach($booking->details as $detail)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $detail->roomType->name }} ({{ $detail->quantity }}x) × {{ $detail->nights }} night(s)</span>
                            <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($detail->subtotal, 2) }}</strong>
                        </div>
                    @endforeach
                    
                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->total_price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->tax_amount, 2) }}</strong>
                    </div>
                    @if($booking->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount:</span>
                        <strong>-{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->discount_amount, 2) }}</strong>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fs-5"><strong>Total:</strong></span>
                        <span class="fs-5 text-primary"><strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</strong></span>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> 
                        <small>Payment instructions will be sent via email.</small>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-list-check"></i> Next Steps</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Check your email for confirmation</li>
                        <li class="mb-2">Complete payment when requested</li>
                        <li class="mb-2">Arrive on your check-in date</li>
                        <li>Present your booking number at reception</li>
                    </ol>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-calendar-check"></i> View My Bookings
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-house"></i> Back to Home
                    </a>
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-door-open"></i> Browse More Rooms
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Information -->
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="bi bi-exclamation-circle text-warning"></i> Important Information</h5>
            <ul class="mb-0">
                <li>Please bring a valid ID for check-in</li>
                <li>After confirming your booking, please note that cancellations are not allowed within 48 hours before check-in</li>
                <li>If you have any questions, contact us at <strong>{{ \App\Models\Setting::getValue('contact_email', 'info@hotel.com') }}</strong></li>
                <li>Booking status: <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection
