@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">{{ $customer->name }}</h3>
            <p class="text-muted small mb-0">Customer Profile & Transaction History</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light border rounded-3 px-3 fw-bold shadow-sm">
                <i class="fas fa-chevron-left me-1 small"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-4 p-3 me-3">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Bookings</h6>
                        <h4 class="fw-bold mb-0">{{ $totalBookings }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success-subtle text-success rounded-4 p-3 me-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Spent</h6>
                        <h4 class="fw-bold mb-0 text-success">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($totalSpent, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning rounded-4 p-3 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small fw-bold text-uppercase mb-1">Upcoming</h6>
                        <h4 class="fw-bold mb-0">{{ $upcomingBookings }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Profile Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Full Name</label>
                            <p class="h6 fw-bold mb-0 text-dark">{{ $customer->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Email Address</label>
                            <p class="h6 fw-bold mb-0 text-dark">{{ $customer->email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Phone Number</label>
                            <p class="h6 fw-bold mb-0 text-dark">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Member Since</label>
                            <p class="h6 fw-bold mb-0 text-dark">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-12">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Address</label>
                            <p class="h6 fw-bold mb-0 text-dark">{{ $customer->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Recent Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 small fw-bold text-muted text-uppercase">Booking #</th>
                                    <th class="border-0 small fw-bold text-muted text-uppercase">Check-In</th>
                                    <th class="border-0 small fw-bold text-muted text-uppercase">Check-Out</th>
                                    <th class="border-0 small fw-bold text-muted text-uppercase">Amount</th>
                                    <th class="border-0 small fw-bold text-muted text-uppercase">Status</th>
                                    <th class="pe-4 border-0 text-end small fw-bold text-muted text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->bookings as $booking)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">#{{ $booking->booking_number }}</td>
                                    <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    <td class="fw-bold">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}-subtle text-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-light border rounded-3 px-3 fw-bold">
                                            Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No booking history found for this customer.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div>
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="avatar-circle mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                        <p class="text-muted small mb-4">{{ $customer->email }}</p>
                        
                        <hr class="opacity-25 my-4">

                        @if(auth()->user()->hasPermission('customers.edit'))
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning w-100 py-3 rounded-3 fw-bold shadow-sm mb-3">
                                <i class="fas fa-edit me-2"></i> Edit Profile
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('customers.delete'))
                            <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 py-2 border-0 opacity-75 small" data-message="Are you sure you want to delete this customer?">
                                    <i class="fas fa-trash me-2"></i> Delete Customer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase">Account Security</h6>
                        <div class="d-flex align-items-center mb-0">
                            <i class="fas fa-shield-alt text-success me-3"></i>
                            <span class="small text-muted">Account is active and verified.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-light { background-color: #f8fafc !important; }
    .table thead th { letter-spacing: 0.05rem; }
    .table tbody td { border-bottom: 1px solid #f1f5f9; padding-top: 1rem; padding-bottom: 1rem; }
    .avatar-circle { border: 4px solid rgba(255,255,255,0.1); }
</style>
@endsection