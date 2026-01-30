@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Settings</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0">System Configuration</h3>
        </div>
    </div>

    @php
        $canEdit = auth()->user()->hasPermission('settings.edit');
    @endphp

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Hotel Identity</h5>
                                <p class="text-muted small mb-0">Public information used for invoices and emails.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="hotel_name" class="form-label small fw-bold text-muted text-uppercase">Hotel Name</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle @error('hotel_name') is-invalid @enderror" 
                                       id="hotel_name" name="hotel_name" 
                                       value="{{ old('hotel_name', $settings->get('hotel_name')?->value ?? 'Grand Hotel') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required>
                                @error('hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label small fw-bold text-muted text-uppercase">Support Email</label>
                                <input type="email" class="form-control rounded-3 border-light-subtle @error('contact_email') is-invalid @enderror" 
                                       id="contact_email" name="contact_email" 
                                       value="{{ old('contact_email', $settings->get('contact_email')?->value ?? 'info@hotel.com') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required>
                                @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="contact_phone" class="form-label small fw-bold text-muted text-uppercase">Business Phone</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle @error('contact_phone') is-invalid @enderror" 
                                       id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone', $settings->get('contact_phone')?->value ?? '') }}"
                                       {{ !$canEdit ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-12">
                                <label for="contact_address" class="form-label small fw-bold text-muted text-uppercase">Physical Address</label>
                                <textarea class="form-control rounded-3 border-light-subtle @error('contact_address') is-invalid @enderror" 
                                          id="contact_address" name="contact_address" rows="2"
                                          {{ !$canEdit ? 'disabled' : '' }}>{{ old('contact_address', $settings->get('contact_address')?->value ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle text-success rounded-3 p-2 me-3">
                                <i class="bi bi-currency-dollar fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Financial Settings</h5>
                                <p class="text-muted small mb-0">Manage currency formatting and tax calculations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="currency" class="form-label small fw-bold text-muted text-uppercase">Currency Code</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle" id="currency" name="currency" 
                                       value="{{ old('currency', $settings->get('currency')?->value ?? 'USD') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required maxlength="10">
                                <small class="text-muted extra-small">e.g., USD, EUR</small>
                            </div>
                            <div class="col-md-4">
                                <label for="currency_symbol" class="form-label small fw-bold text-muted text-uppercase">Symbol</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle" id="currency_symbol" name="currency_symbol" 
                                       value="{{ old('currency_symbol', $settings->get('currency_symbol')?->value ?? '$') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required maxlength="5">
                                <small class="text-muted extra-small">e.g., $, €</small>
                            </div>
                            <div class="col-md-4">
                                <label for="tax_rate" class="form-label small fw-bold text-muted text-uppercase">Tax Rate (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control rounded-start-3 border-light-subtle" id="tax_rate" name="tax_rate" 
                                           value="{{ old('tax_rate', $settings->get('tax_rate')?->value ?? 10) }}" {{ !$canEdit ? 'disabled' : '' }} required>
                                    <span class="input-group-text bg-light border-light-subtle rounded-end-3">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning-subtle text-warning rounded-3 p-2 me-3">
                                <i class="bi bi-calendar-check fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Booking Policy</h5>
                                <p class="text-muted small mb-0">Standardize check-in and check-out windows.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="check_in_time" class="form-label small fw-bold text-muted text-uppercase">Check-in Time</label>
                                <input type="time" class="form-control rounded-3 border-light-subtle" id="check_in_time" name="check_in_time" 
                                       value="{{ old('check_in_time', $settings->get('check_in_time')?->value ?? '14:00') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out_time" class="form-label small fw-bold text-muted text-uppercase">Check-out Time</label>
                                <input type="time" class="form-control rounded-3 border-light-subtle" id="check_out_time" name="check_out_time" 
                                       value="{{ old('check_out_time', $settings->get('check_out_time')?->value ?? '12:00') }}" 
                                       {{ !$canEdit ? 'disabled' : '' }} required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3">Publish Changes</h6>
                        
                        @if($canEdit)
                            <div class="alert bg-primary-subtle border-0 rounded-3 small text-primary mb-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Changes made here will affect all current operations and invoices.
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-save me-1"></i> Save All Settings
                                </button>
                                <button type="reset" class="btn btn-light border rounded-3 py-2 text-muted fw-bold">
                                    Discard Changes
                                </button>
                            </div>
                        @else
                            <div class="bg-light rounded-4 p-4 text-center border">
                                <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                                    <i class="bi bi-shield-lock fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark">ReadOnly Access</h6>
                                <p class="text-muted extra-small mb-0">You don't have sufficient permissions to modify system settings. Contact your Super Admin.</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0 py-3 px-4 rounded-bottom-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">System Status</span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2">Operational</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.72rem; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }

    .form-control {
        padding: 0.6rem 0.75rem;
    }
    .form-control:focus {
        border-color: #4338ca;
        box-shadow: 0 0 0 0.25rem rgba(67, 56, 202, 0.1);
    }
    .form-control:disabled {
        background-color: #f8fafc;
        border-color: #f1f5f9;
        cursor: not-allowed;
    }
</style>
@endsection