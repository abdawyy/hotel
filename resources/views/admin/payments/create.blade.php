@extends('layouts.admin')

@section('title', 'Create Payment')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-muted">Payments</a></li>
                    <li class="breadcrumb-item small active text-primary" aria-current="page">New Payment</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark">Create New Payment</h3>
            <p class="text-muted small mb-0">Manually record a transaction for an existing booking.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary rounded-3 px-3 fw-bold">
                Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('admin.payments.store') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="booking_id" class="form-label small fw-bold text-muted text-uppercase">Associated Booking <span class="text-danger">*</span></label>
                                <select name="booking_id" id="booking_id" class="form-select border-light-subtle rounded-3 shadow-none py-2 @error('booking_id') is-invalid @enderror" required>
                                    <option value="">Select Booking</option>
                                    @foreach($bookings as $booking)
                                        <option value="{{ $booking->id }}" {{ old('booking_id', request('booking_id')) == $booking->id ? 'selected' : '' }}>
                                            {{ $booking->booking_number }} — {{ $booking->guest_name }} 
                                            ({{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text extra-small">Search by booking number or guest name.</div>
                                @error('booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label small fw-bold text-muted text-uppercase">Amount Received <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle rounded-start-3 text-muted fw-bold">
                                        {{ \App\Models\Setting::getValue('currency_symbol', '$') }}
                                    </span>
                                    <input type="number" step="0.01" class="form-control border-light-subtle rounded-end-3 shadow-none py-2 @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" min="0" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_method" class="form-label small fw-bold text-muted text-uppercase">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="payment_method" class="form-select border-light-subtle rounded-3 shadow-none py-2 @error('payment_method') is-invalid @enderror" required>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label small fw-bold text-muted text-uppercase">Initial Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select border-light-subtle rounded-3 shadow-none py-2 @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="transaction_id" class="form-label small fw-bold text-muted text-uppercase">Transaction ID / Reference (Optional)</label>
                                <input type="text" class="form-control border-light-subtle rounded-3 shadow-none py-2 @error('transaction_id') is-invalid @enderror" 
                                       id="transaction_id" name="transaction_id" placeholder="Enter bank reference or gateway ID" value="{{ old('transaction_id') }}">
                                @error('transaction_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="notes" class="form-label small fw-bold text-muted text-uppercase">Payment Notes</label>
                                <textarea class="form-control border-light-subtle rounded-3 shadow-none @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Add any relevant information about this transaction...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-muted border">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="me-1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd !important;
        background-color: #fff !important;
    }

    .form-label {
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        border-right: none;
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25) !important;
    }
</style>
@endsection