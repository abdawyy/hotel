@extends('layouts.public')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<!-- Hero Section with Search -->
<div class="hero-section bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">{{ __('public.welcome_to') }} {{ \App\Models\Setting::getValue('hotel_name', 'Grand Hotel') }}</h1>
                <p class="lead mb-4">{{ __('public.experience_luxury') }}</p>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">{{ __('public.find_perfect_room') }}</h4>
                        <form action="{{ route('rooms.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('public.check_in') }}</label>
                                    <input type="date" name="check_in" class="form-control" value="{{ request('check_in') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('public.check_out') }}</label>
                                    <input type="date" name="check_out" class="form-control" value="{{ request('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('public.adults') }}</label>
                                    <input type="number" name="adults" class="form-control" value="{{ request('adults', 1) }}" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('public.children') }}</label>
                                    <input type="number" name="children" class="form-control" value="{{ request('children', 0) }}" min="0">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="bi bi-search"></i> {{ __('public.search_rooms') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Rooms -->
<div class="container mb-5">
    <h2 class="text-center mb-5">{{ __('public.featured_rooms') }}</h2>
    <div class="row g-4">
        @forelse($featuredRooms as $room)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    @php
                        $displayImage = $room->primaryImage ?? $room->images->first();
                    @endphp
                    @if($displayImage)
                        <img src="{{ asset('storage/' . $displayImage->image_path) }}" class="card-img-top" alt="{{ $room->name }}" style="height: 250px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $room->name }}</h5>
                        <p class="text-muted small">{{ Str::limit($room->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary mb-0">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($room->price_per_night, 2) }}/{{ __('public.per_night') }}</span>
                            <span class="badge bg-info"><i class="bi bi-people"></i> {{ $room->max_guests }} {{ __('public.guests') }}</span>
                        </div>
                        <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-primary w-100">{{ __('public.view_details') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">{{ __('public.no_rooms_available') }}</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Amenities Section -->
@if($amenities->count() > 0)
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">{{ __('public.hotel_amenities') }}</h2>
        <div class="row g-4">
            @foreach($amenities as $amenity)
                <div class="col-md-3 text-center">
                    <div class="p-4">
                        @if($amenity->icon)
                            <i class="{{ $amenity->icon }} text-primary" style="font-size: 3rem;"></i>
                        @else
                            <i class="bi bi-star text-primary" style="font-size: 3rem;"></i>
                        @endif
                        <h5 class="mt-3">{{ $amenity->name }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

