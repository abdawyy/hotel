@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-muted">Payments</a></li>
                    <li class="breadcrumb-item small active text-primary" aria-current="page">Details</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark">Payment #{{ $payment->payment_number }}</h3>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-bold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Transaction Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase border-0 ps-0" style="width: 30%;">Payment Number</td>
                                    <td class="fw-bold text-dark border-0">{{ $payment->payment_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Booking Reference</td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="text-primary fw-bold text-decoration-none">
                                            #{{ $payment->booking->booking_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Amount Paid</td>
                                    <td>
                                        <h4 class="fw-bold text-dark mb-0">
                                            {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($payment->amount, 2) }}
                                        </h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Payment Method</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar-sm bg-light rounded-2 d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                            </span>
                                            <span class="text-dark fw-semibold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Current Status</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'completed' => 'bg-success-subtle text-success border-success-subtle',
                                                'failed' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'pending' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                'refunded' => 'bg-info-subtle text-info border-info-subtle'
                                            ];
                                            $currentClass = $statusClasses[$payment->status] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge rounded-pill border {{ $currentClass }} px-3 py-2" style="font-weight: 600;">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($payment->transaction_id)
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Transaction ID</td>
                                    <td class="text-dark font-monospace">{{ $payment->transaction_id }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted small fw-bold text-uppercase ps-0">Payment Date</td>
                                    <td class="text-dark">{{ $payment->created_at->format('M d, Y') }} <span class="text-muted ms-1 small">{{ $payment->created_at->format('h:i A') }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($payment->notes)
                    <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Administrative Notes</label>
                        <p class="mb-0 text-dark small italic">"{{ $payment->notes }}"</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Management Actions</h6>
                    <div class="d-grid gap-2">
                        @if(auth()->user()->hasPermission('payments.edit'))
                            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-warning rounded-3 fw-bold py-2 shadow-sm text-white">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                Edit Details
                            </a>
                        @endif

                        @if($payment->payment_method === 'paypal' && $payment->status === 'completed' && auth()->user()->hasPermission('payments.edit'))
                            <button type="button" class="btn btn-outline-danger rounded-3 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#refundModal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                Process Refund
                            </button>
                        @endif

                        <button onclick="window.print()" class="btn btn-light rounded-3 fw-bold py-2 border">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Print Receipt
                        </button>

                        @if(auth()->user()->hasPermission('payments.delete'))
                            <hr class="my-2 opacity-50">
                            <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" class="delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger border-0 rounded-3 fw-bold w-100 py-2">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    Void Transaction
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <div class="avatar-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold" style="width: 64px; height: 64px; font-size: 1.5rem;">
                        {{ strtoupper(substr($payment->booking->guest_name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $payment->booking->guest_name }}</h5>
                    <p class="text-muted small mb-3">{{ $payment->booking->guest_email }}</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6 bg-light p-2 rounded-3">
                            <span class="extra-small text-muted d-block text-uppercase fw-bold">Check-In</span>
                            <span class="small fw-bold text-dark">{{ $payment->booking->check_in_date->format('M d, Y') }}</span>
                        </div>
                        <div class="col-6 bg-light p-2 rounded-3">
                            <span class="extra-small text-muted d-block text-uppercase fw-bold">Check-Out</span>
                            <span class="small fw-bold text-dark">{{ $payment->booking->check_out_date->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.bookings.show', $payment->booking->id) }}" class="btn btn-primary-subtle w-100 rounded-3 small fw-bold">
                        View Full Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.65rem; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .btn-primary-subtle { background-color: #eef2ff; color: #4338ca; border: none; }
    .btn-primary-subtle:hover { background-color: #4338ca; color: white; }
    
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #991b1b !important; }
    .bg-info-subtle { background-color: #ecfeff !important; color: #0e7490 !important; }
    
    .table > :not(caption) > * > * { padding: 1rem 0.75rem; }
    .font-monospace { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; }

    @media print {
        .btn, .breadcrumb, .card-header, .col-lg-4 { display: none !important; }
        .col-lg-8 { width: 100% !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
    }
</style>

<!-- Refund Modal -->
@if($payment->payment_method === 'paypal' && $payment->status === 'completed')
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="refundModalLabel">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 text-danger"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    Process PayPal Refund
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning rounded-3 mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will initiate a refund through PayPal. This action cannot be undone.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Original Payment Amount</label>
                        <p class="fs-4 fw-bold text-dark mb-0">
                            {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($payment->amount, 2) }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <label for="refund_amount" class="form-label small fw-bold text-muted text-uppercase">Refund Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="refund_amount" 
                                   name="amount" 
                                   value="{{ $payment->amount }}" 
                                   min="0.01" 
                                   max="{{ $payment->amount }}" 
                                   step="0.01"
                                   required>
                        </div>
                        <div class="form-text">Leave as full amount for complete refund, or enter partial amount.</div>
                    </div>

                    <div class="mb-3">
                        <label for="refund_reason" class="form-label small fw-bold text-muted text-uppercase">Reason for Refund</label>
                        <textarea class="form-control" id="refund_reason" name="reason" rows="3" placeholder="Enter the reason for this refund..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection