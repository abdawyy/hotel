@extends('layouts.public')

@section('title', $roomType->name . ' - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('public.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">{{ __('public.rooms') }}</a></li>
            <li class="breadcrumb-item active">{{ $roomType->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Room Images -->
        <div class="col-md-8">
            @if($roomType->images->count() > 0)
                <div id="roomCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($roomType->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" alt="{{ $roomType->name }}" style="height: 500px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                    @if($roomType->images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-secondary d-flex align-items-center justify-content-center mb-4" style="height: 500px;">
                    <i class="bi bi-image text-white" style="font-size: 5rem;"></i>
                </div>
            @endif

            <!-- Room Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">{{ $roomType->name }}</h2>
                    <p class="text-muted">{{ $roomType->description }}</p>

                    <!-- Room Features -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <h5>{{ __('public.room_details') }}</h5>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-people"></i> <strong>{{ __('public.max_guests') }}:</strong> {{ $roomType->max_guests }}</li>
                                <li><i class="bi bi-person"></i> <strong>{{ __('public.max_adults') }}:</strong> {{ $roomType->max_adults }}</li>
                                <li><i class="bi bi-emoji-smile"></i> <strong>{{ __('public.max_children') }}:</strong> {{ $roomType->max_children }}</li>
                                <!-- <li><i class="bi bi-door-open"></i> <strong>{{ __('public.total_rooms') }}:</strong> {{ $roomType->total_rooms }}</li> -->
                                @php
                                    $availableCount = request('check_in') && request('check_out') 
                                        ? $roomType->getAvailableRoomsCount(\Carbon\Carbon::parse(request('check_in')), \Carbon\Carbon::parse(request('check_out'))) 
                                        : $roomType->rooms()->where('status', 'available')->count();
                                @endphp
                                <li><i class="bi bi-check-circle text-success"></i> <strong>{{ __('public.available_rooms') }}:</strong> {{ $availableCount }}</li>
                            </ul>
                        </div>
                        @if($roomType->amenities->count() > 0)
                        <div class="col-md-6 mb-3">
                            <h5>{{ __('public.hotel_amenities') }}</h5>
                            <ul class="list-unstyled">
                                @foreach($roomType->amenities as $amenity)
                                    <li><i class="bi bi-check-circle text-success"></i> {{ $amenity->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Sidebar -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('public.book_this_room') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h3 class="text-primary">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($roomType->price_per_night, 2) }}</h3>
                        <p class="text-muted small mb-0">{{ __('public.per_night') }}</p>
                    </div>

                    <form action="{{ route('booking.create') }}" method="GET">
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                        
                        <div class="mb-3">
                            <label for="check_in" class="form-label">{{ __('public.check_in_date') }}</label>
                            <input type="text" class="form-control" id="check_in" name="check_in" readonly
                                   placeholder="{{ __('public.check_in_date') }}" value="{{ request('check_in') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="check_out" class="form-label">{{ __('public.check_out_date') }}</label>
                            <input type="text" class="form-control" id="check_out" name="check_out" readonly
                                   placeholder="{{ __('public.check_out_date') }}" value="{{ request('check_out') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="adults" class="form-label">{{ __('public.adults') }}</label>
                            <input type="number" class="form-control" id="adults" name="adults" 
                                   min="1" max="{{ $roomType->max_adults }}" value="{{ request('adults', 1) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="children" class="form-label">{{ __('public.children') }}</label>
                            <input type="number" class="form-control" id="children" name="children" 
                                   min="0" max="{{ $roomType->max_children }}" value="{{ request('children', 0) }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-calendar-check"></i> {{ __('public.book_now') }}
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ __('public.back_to_rooms') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Rooms -->
    @if($similarRooms->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">{{ __('public.similar_rooms') }}</h3>
            <div class="row g-4">
                @foreach($similarRooms as $similarRoom)
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            @php
                                $displayImage = $similarRoom->primaryImage ?? $similarRoom->images->first();
                            @endphp
                            @if($displayImage)
                                <img src="{{ asset('storage/' . $displayImage->image_path) }}" 
                                     class="card-img-top" alt="{{ $similarRoom->name }}" 
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h6 class="card-title">{{ $similarRoom->name }}</h6>
                                <p class="text-primary mb-2"><strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($similarRoom->price_per_night, 2) }}/{{ __('public.per_night') }}</strong></p>
                                <a href="{{ route('rooms.show', $similarRoom->id) }}" class="btn btn-sm btn-primary w-100">{{ __('public.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function() {
    // Only dates that exceed reservation limit (fully booked nights) - from backend
    var unavailableDates = @json($unavailableDates ?? []);
    var today = '{{ date('Y-m-d') }}';

    function dateStr(d) {
        if (typeof d === 'string') return d;
        if (!d || !d.getTime) return '';
        var y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2, '0'), day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }
    function addDayStr(s) {
        var d = new Date(s + 'T12:00:00');
        d.setDate(d.getDate() + 1);
        return dateStr(d);
    }
    // Check-out date D invalid if the night before (D-1) is fully booked
    var disabledCheckOutBase = unavailableDates.map(function(d) { return addDayStr(d); });

    var checkInEl = document.getElementById('check_in');
    var checkOutEl = document.getElementById('check_out');
    if (!checkInEl || !checkOutEl) return;

    // Pass array of date strings so Flatpickr disables ONLY those specific dates (not the whole calendar)
    var checkOutFp;
    var checkInFp = flatpickr(checkInEl, {
        minDate: today,
        dateFormat: 'Y-m-d',
        disable: unavailableDates,
        onChange: function(sel, dateStrVal) {
            if (!dateStrVal) return;
            checkOutFp.set('minDate', dateStrVal);
            checkOutFp.set('disable', function(d) {
                var dStr = dateStr(d);
                if (!dStr || dStr <= dateStrVal) return true;
                var current = new Date(dateStrVal + 'T12:00:00');
                while (dateStr(current) < dStr) {
                    if (unavailableDates.indexOf(dateStr(current)) !== -1) return true;
                    current.setDate(current.getDate() + 1);
                }
                return false;
            });
            if (checkOutEl.value && checkOutEl.value <= dateStrVal) checkOutFp.clear();
        }
    });

    checkOutFp = flatpickr(checkOutEl, {
        minDate: today,
        dateFormat: 'Y-m-d',
        disable: disabledCheckOutBase,
        onChange: function() {}
    });

    if (checkInEl.value && checkInFp.selectedDates.length) {
        checkInFp.config.onChange(checkInFp.selectedDates, checkInEl.value);
    }
})();
</script>
@endpush
@endsection

