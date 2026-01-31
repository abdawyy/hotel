@extends('layouts.public')

@section('title', 'Book ' . $roomType->name . ' - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('public.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">{{ __('public.rooms') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.show', $roomType->id) }}">{{ $roomType->name }}</a></li>
            <li class="breadcrumb-item active">{{ __('public.book_now') }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('public.booking_information') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf

                        <!-- Hidden fields -->
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                        <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                        <input type="hidden" name="adults" value="{{ request('adults', 1) }}">
                        <input type="hidden" name="children" value="{{ request('children', 0) }}">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>{{ __('public.room_details') }}</h5>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('public.room_type_label') }}:</label>
                                <p class="form-control-plaintext"><strong>{{ $roomType->name }}</strong></p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('public.check_in') }}:</label>
                                <p class="form-control-plaintext">{{ $checkIn->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('public.check_out') }}:</label>
                                <p class="form-control-plaintext">{{ $checkOut->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('public.nights_label') }}:</label>
                                <p class="form-control-plaintext">{{ $nights }} {{ __('public.nights') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('public.guests') }}:</label>
                                <p class="form-control-plaintext">{{ request('adults', 1) }} {{ __('public.adults') }}, {{ request('children', 0) }} {{ __('public.children') }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>{{ __('public.guest_information') }}</h5>
                                <hr>
                            </div>
                            @auth
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Booking as: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                                </div>
                            </div>
                            @endauth
                            <div class="col-md-6 mb-3">
                                <label for="guest_name" class="form-label">{{ __('public.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" 
                                       value="{{ old('guest_name', Auth::check() ? Auth::user()->name : '') }}" required>
                                @error('guest_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_email" class="form-label">{{ __('public.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('guest_email') is-invalid @enderror" 
                                       id="guest_email" name="guest_email" 
                                       value="{{ old('guest_email', Auth::check() ? Auth::user()->email : '') }}" required>
                                @error('guest_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guest_phone" class="form-label">{{ __('public.phone_number') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('guest_phone') is-invalid @enderror" 
                                       id="guest_phone" name="guest_phone" 
                                       value="{{ old('guest_phone', Auth::check() ? Auth::user()->phone : '') }}" required>
                                @error('guest_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="guest_address" class="form-label">{{ __('public.address') }}</label>
                                <textarea class="form-control @error('guest_address') is-invalid @enderror" 
                                          id="guest_address" name="guest_address" rows="2">{{ old('guest_address', Auth::check() ? Auth::user()->address : '') }}</textarea>
                                @error('guest_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="special_requests" class="form-label">{{ __('public.special_requests') }}</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                          id="special_requests" name="special_requests" rows="3" 
                                          placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rooms.show', $roomType->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> {{ __('public.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-check"></i> {{ __('public.confirm_booking_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('public.booking_summary') }}</h5>
                </div>
                <div class="card-body">
                    @if($roomType->primaryImage ?? $roomType->images->first())
                        @php $image = $roomType->primaryImage ?? $roomType->images->first(); @endphp
                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                             class="img-fluid rounded mb-3" 
                             alt="{{ $roomType->name }}" 
                             style="height: 150px; width: 100%; object-fit: cover;">
                    @endif
                    
                    <h6>{{ $roomType->name }}</h6>
                    <p class="text-muted small mb-3">{{ Str::limit($roomType->description, 100) }}</p>
                    <p class="small mb-3"><i class="bi bi-info-circle"></i> <strong>{{ $availableCount }}</strong> {{ __('public.rooms_available') }}</p>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('public.price_per_night_label') }}:</span>
                        <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($pricePerNight, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('public.nights') }}:</span>
                        <strong>{{ $nights }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('public.subtotal') }}:</span>
                        <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('public.tax') }} ({{ $taxRate }}%):</span>
                        <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($taxAmount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fs-5"><strong>{{ __('public.total') }}:</strong></span>
                        <span class="fs-5 text-primary"><strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($totalPrice, 2) }}</strong></span>
                    </div>

                    @if($availableCount > 0)

                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i> {{ __('public.limited_availability') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
