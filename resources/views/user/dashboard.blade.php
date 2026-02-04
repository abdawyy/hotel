@extends('layouts.public')

@section('title', 'My Dashboard - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> {{ __('public.my_dashboard') }}</h2>
            <p class="text-muted">{{ __('admin.welcome') }}, {{ $user->name }}!</p>
        </div>
    </div>

    <!-- Upcoming Bookings -->
    @if($upcomingBookings->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> {{ __('public.upcoming_bookings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('public.booking_number') }}</th>
                                    <th>{{ __('public.room_type') }}</th>
                                    <th>{{ __('public.check_in') }}</th>
                                    <th>{{ __('public.check_out') }}</th>
                                    <th>{{ __('public.status') }}</th>
                                    <th>{{ __('public.total_amount') }}</th>
                                    <th>{{ __('public.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingBookings as $booking)
                                <tr>
                                    <td><strong>{{ $booking->booking_number }}</strong></td>
                                    <td>
                                        @foreach($booking->details as $detail)
                                            {{ $detail->roomType->name }} ({{ $detail->quantity }}x)
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</td>
                                    <td>
                                        <a href="{{ route('booking.confirmation', $booking->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> {{ __('public.view') }}
                                        </a>
                                        @if(in_array($booking->status, ['pending', 'confirmed']))
                                            @php
                                                $checkInDate = \Carbon\Carbon::parse($booking->check_in_date);
                                                $now = \Carbon\Carbon::now();
                                                $hoursUntilCheckIn = $now->diffInHours($checkInDate, false);
                                                if ($booking->status === 'pending') {
                                                    $canCancel = true;
                                                    $cancelTitle = '';
                                                } elseif ($booking->status === 'confirmed') {
                                                    $canCancel = $hoursUntilCheckIn >= 48;
                                                    $cancelTitle = $canCancel ? '' : 'Cancellation not allowed within 48 hours of check-in';
                                                } else {
                                                    $canCancel = false;
                                                    $cancelTitle = 'Cannot cancel this booking';
                                                }
                                            @endphp
                                            @if($canCancel)
                                            <button type="button" class="btn btn-sm btn-danger" onclick="openCancelModal('{{ route('booking.cancel', $booking->id) }}')">
                                                <i class="bi bi-x-circle"></i> {{ __('public.cancel') }}
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled title="{{ $cancelTitle }}">
                                                <i class="bi bi-x-circle"></i> {{ __('public.cancel') }}
                                            </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- All Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> {{ __('public.all_my_bookings') }}</h5>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('public.booking_number') }}</th>
                                    <th>{{ __('public.room_type') }}</th>
                                    <th>{{ __('public.check_in') }}</th>
                                    <th>{{ __('public.check_out') }}</th>
                                    <th>{{ __('public.nights') }}</th>
                                    <th>{{ __('public.status') }}</th>
                                    <th>{{ __('public.total_amount') }}</th>
                                    <th>{{ __('public.payment_status') }}</th>
                                    <th>{{ __('public.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td><strong>{{ $booking->booking_number }}</strong></td>
                                    <td>
                                        @foreach($booking->details as $detail)
                                            <div>{{ $detail->roomType->name }} ({{ $detail->quantity }}x)</div>
                                        @endforeach
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $booking->status === 'confirmed' ? 'success' :
                                            ($booking->status === 'checked_in' ? 'info' :
                                            ($booking->status === 'checked_out' ? 'primary' :
                                            ($booking->status === 'cancelled' ? 'danger' : 'warning')))
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $paidAmount = $booking->payments->where('status', 'completed')->sum('amount');
                                            $remainingBalance = $booking->final_amount - $paidAmount;
                                            $isPaid = $remainingBalance <= 0;
                                        @endphp
                                        @if($isPaid)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> {{ __('public.paid') }}
                                            </span>
                                        @elseif($paidAmount > 0)
                                            <span class="badge bg-info">
                                                {{ __('public.partial') }}
                                            </span>
                                            <small class="d-block text-muted">
                                                {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($paidAmount, 2) }} paid
                                            </small>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                {{ __('public.pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm">
                                            <a href="{{ route('booking.confirmation', $booking->id) }}" class="btn btn-info">
                                                <i class="bi bi-eye"></i> {{ __('public.view') }}
                                            </a>
                                            @if(!$isPaid && in_array($booking->status, ['pending', 'confirmed']))
                                                <a href="{{ route('paypal.payment', $booking->id) }}" class="btn btn-warning">
                                                    <i class="bi bi-credit-card"></i> {{ __('public.pay_now') }}
                                                </a>
                                            @endif
                                            @if(in_array($booking->status, ['pending', 'confirmed']))
                                            @php
                                                $checkInDate = \Carbon\Carbon::parse($booking->check_in_date);
                                                $now = \Carbon\Carbon::now();
                                                $hoursUntilCheckIn = $now->diffInHours($checkInDate, false);
                                                if ($booking->status === 'pending') {
                                                    $canCancel = true;
                                                    $cancelTitle = '';
                                                } elseif ($booking->status === 'confirmed') {
                                                    $canCancel = $hoursUntilCheckIn >= 48;
                                                    $cancelTitle = $canCancel ? '' : 'Cancellation not allowed within 48 hours of check-in';
                                                } else {
                                                    $canCancel = false;
                                                    $cancelTitle = 'Cannot cancel this booking';
                                                }
                                            @endphp
                                            @endif
                                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-circle"></i> {{ __('public.cancel') }}
                                                </button>
                                            </form>
                                            @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled title="{{ $cancelTitle }}">
                                                <i class="bi bi-x-circle"></i> {{ __('public.cancel') }}
                                            </button>
                                            @endif
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> {{ __('public.no_bookings') }}
                        <a href="{{ route('rooms.index') }}" class="btn btn-primary mt-2">{{ __('public.browse_rooms') }}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this booking? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Booking</button>
                <form id="cancelForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCancelModal(url) {
    document.getElementById('cancelForm').action = url;
    var modal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
    modal.show();
}
</script>
@endpush

@endsection

