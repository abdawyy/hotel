@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-link text-muted p-0 mb-2 text-decoration-none small">
                <i class="fas fa-arrow-left me-1"></i> Back to Bookings
            </a>
            <h3 class="fw-bold text-dark mb-0">
                Booking #{{ $booking->booking_number }}
                <span class="ms-2 badge rounded-pill border {{ 
                    $booking->status === 'confirmed' ? 'bg-success-subtle text-success border-success-subtle' :
                    ($booking->status === 'checked_in' ? 'bg-info-subtle text-info border-info-subtle' :
                    ($booking->status === 'checked_out' ? 'bg-primary-subtle text-primary border-primary-subtle' :
                    ($booking->status === 'cancelled' ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-warning-subtle text-warning border-warning-subtle')))
                }} px-3 py-2" style="font-size: 0.8rem;">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </h3>
        </div>
        <div class="text-end">
            <span class="text-muted small d-block">Reserved on</span>
            <span class="fw-bold text-dark">{{ $booking->created_at->format('M d, Y | H:i') }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Guest Information</h6>
                            <div class="d-flex align-items-center">
                                <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                                    {{ strtoupper(substr($booking->guest_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $booking->guest_name }}</h5>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-envelope me-1"></i> {{ $booking->guest_email }}<br>
                                        @if($booking->guest_phone) <i class="fas fa-phone me-1"></i> {{ $booking->guest_phone }} @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 border-start-md ps-md-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Stay Details</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded-3">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Check-In</small>
                                        <span class="fw-bold">{{ $booking->check_in_date->format('D, M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded-3">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Check-Out</small>
                                        <span class="fw-bold">{{ $booking->check_out_date->format('D, M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($booking->special_requests)
                    <div class="bg-warning-subtle border border-warning-subtle p-3 rounded-3 mb-0">
                        <h6 class="fw-bold text-warning-emphasis small"><i class="fas fa-comment-dots me-2"></i>Special Requests</h6>
                        <p class="mb-0 small text-dark opacity-75">{{ $booking->special_requests }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0">Room Inventory & Rates</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted extra-small text-uppercase">
                                <th class="ps-4 border-0">Room Type</th>
                                <th class="border-0">Qty</th>
                                <th class="border-0">Price/Night</th>
                                <th class="border-0">Assigned Room</th>
                                <th class="pe-4 border-0 text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->details as $detail)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @php $displayImage = $detail->roomType->primaryImage ?? $detail->roomType->images->first(); @endphp
                                        <img src="{{ $displayImage ? asset('storage/' . $displayImage->image_path) : 'https://via.placeholder.com/60' }}" 
                                             class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <span class="fw-bold text-dark">{{ $detail->roomType->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($detail->price_per_night, 2) }}</td>
                                <td>
                                    @if($detail->room)
                                        <span class="badge bg-primary px-2">{{ $detail->room->room_number }}</span>
                                    @else
                                        <span class="text-muted small">Pending...</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end fw-bold">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($detail->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($booking->payments->count() > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0">Payment History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted extra-small text-uppercase">
                                <th class="ps-4 border-0">Ref #</th>
                                <th class="border-0">Method</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="pe-4 border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td class="ps-4 small fw-bold text-muted">{{ $payment->payment_number }}</td>
                                <td class="small">{{ ucfirst($payment->payment_method) }}</td>
                                <td>
                                    <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : 'bg-warning' }} rounded-pill" style="font-size: 10px;">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($payment->amount, 2) }}</td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-light border text-dark">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-dark text-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Operations</h6>
                </div>
                <div class="card-body p-4">
                    @if(auth()->user()->hasPermission('bookings.edit'))
                        
                        @if($booking->status !== 'cancelled' && $booking->status !== 'checked_out')
                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="mb-3">
                            @csrf @method('PUT')
                            @php
                                $nextStatus = $booking->status === 'pending' ? 'confirmed' : ($booking->status === 'confirmed' ? 'checked_in' : 'checked_out');
                                $btnText = $booking->status === 'pending' ? 'Confirm Booking' : ($booking->status === 'confirmed' ? 'Process Check-In' : 'Process Check-Out');
                                $btnIcon = $booking->status === 'pending' ? 'fa-check-circle' : ($booking->status === 'confirmed' ? 'fa-key' : 'fa-sign-out-alt');
                            @endphp
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                                <i class="fas {{ $btnIcon }} me-2"></i> {{ $btnText }}
                            </button>
                        </form>
                        @endif

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-outline-dark w-100 py-2 rounded-3 small">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </div>
                            <div class="col-6">
                                @if(auth()->user()->hasPermission('payments.create'))
                                <a href="{{ route('admin.payments.create', ['booking_id' => $booking->id]) }}" class="btn btn-outline-success w-100 py-2 rounded-3 small">
                                    <i class="fas fa-plus me-1"></i> Pay
                                </a>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        @if($booking->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.bookings.cancel', $booking->id) }}" class="mb-2 delete-form">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger w-100 text-decoration-none small" data-message="Cancel this reservation?">
                                <i class="fas fa-times-circle me-1"></i> Cancel Booking
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->hasPermission('bookings.delete'))
                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link text-muted w-100 text-decoration-none extra-small" data-message="Delete permanently?">
                                <i class="fas fa-trash-alt me-1"></i> Delete Record
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark text-uppercase small mb-4">Financial Summary</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Room Charges</span>
                        <span class="fw-semibold text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Taxes & Fees</span>
                        <span class="fw-semibold text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->tax_amount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mb-4">
                        <span class="fw-bold text-dark">Grand Total</span>
                        <span class="fs-4 fw-bold text-primary">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</span>
                    </div>

                    @php
                        $totalPaid = $booking->payments->where('status', 'completed')->sum('amount');
                        $balance = $booking->final_amount - $totalPaid;
                    @endphp

                    <div class="p-3 rounded-3 {{ $balance > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-uppercase fw-bold opacity-75" style="font-size: 10px;">Amount Paid</small>
                            <span class="small fw-bold">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-uppercase fw-bold opacity-75" style="font-size: 10px;">Balance Due</small>
                            <span class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    
    @media (min-width: 768px) {
        .border-start-md { border-left: 1px solid #eef2f7 !important; }
    }
</style>
@endsection