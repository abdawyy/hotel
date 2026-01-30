@extends('layouts.public')

@section('title', 'Rooms - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <h1 class="mb-4">{{ __('public.our_rooms') }}</h1>
    
    <!-- Search Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('rooms.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('public.check_in') }}</label>
                        <input type="date" name="check_in" class="form-control" value="{{ request('check_in') }}" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('public.check_out') }}</label>
                        <input type="date" name="check_out" class="form-control" value="{{ request('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('public.adults') }}</label>
                        <input type="number" name="adults" class="form-control" value="{{ request('adults', 1) }}" min="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('public.children') }}</label>
                        <input type="number" name="children" class="form-control" value="{{ request('children', 0) }}" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">{{ __('public.search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rooms Grid -->
    <div class="row g-4">
        @forelse($rooms as $room)
            @php
                $isAvailable = true;
                if (request('check_in') && request('check_out')) {
                    $checkIn = \Carbon\Carbon::parse(request('check_in'));
                    $checkOut = \Carbon\Carbon::parse(request('check_out'));
                    $isAvailable = $room->getAvailableRoomsCount($checkIn, $checkOut) > 0;
                }
            @endphp
            <div class="col-md-4">
                <div class="card h-100 shadow-sm {{ !$isAvailable ? 'opacity-50' : '' }}">
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h5 text-primary mb-0">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($room->price_per_night, 2) }}/{{ __('public.per_night') }}</span>
                            <span class="badge bg-info"><i class="bi bi-people"></i> {{ $room->max_guests }} {{ __('public.guests') }}</span>
                        </div>
                        @php
                            $availableCount = request('check_in') && request('check_out') 
                                ? $room->getAvailableRoomsCount(\Carbon\Carbon::parse(request('check_in')), \Carbon\Carbon::parse(request('check_out'))) 
                                : $room->rooms()->where('status', 'available')->count();
                        @endphp
                        <div class="mb-3">
                            <span class="badge bg-success"><i class="bi bi-door-open"></i> {{ $availableCount }} {{ __('public.available') }}</span>
                        </div>
                        @if($isAvailable)
                            <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-primary w-100">{{ __('public.view_details') }}</a>
                        @else
                            <button class="btn btn-secondary w-100" disabled>{{ __('public.unavailable') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <h5>{{ __('public.no_rooms_found') }}</h5>
                    <p>{{ __('public.adjust_search') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
@endsection

