@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Bookings</h3>
            <p class="text-muted small mb-0">Monitor guest reservations, check-ins, and payment statuses.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if(auth()->user()->hasPermission('bookings.create'))
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm fw-bold">
                    Create New Booking
                </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.bookings.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase">Status</label>
                        <select name="status" class="form-select border-light-subtle rounded-3 shadow-none">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase">Check-in From</label>
                        <input type="date" name="check_in_from" class="form-control border-light-subtle rounded-3 shadow-none" value="{{ request('check_in_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase">Check-in To</label>
                        <input type="date" name="check_in_to" class="form-control border-light-subtle rounded-3 shadow-none" value="{{ request('check_in_to') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Search Guest</label>
                        <input type="text" name="search" class="form-control border-light-subtle rounded-3 shadow-none" placeholder="Booking #, Name, Email" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold">Filter</button>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Search">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 border-0 py-3">Booking Info</th>
                                <th class="border-0 py-3">Guest</th>
                                <th class="border-0 py-3">Stay Dates</th>
                                <th class="border-0 py-3 text-center">Amount</th>
                                <th class="border-0 py-3 text-center">Status</th>
                                <th class="border-0 py-3 text-center">Payment</th>
                                <th class="pe-4 border-0 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">#{{ $booking->booking_number }}</div>
                                        @foreach($booking->details->take(1) as $detail)
                                            <span class="text-muted extra-small d-block">{{ $detail->roomType->name }} ({{ $detail->quantity }}x)</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($booking->guest_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0" style="font-size: 0.85rem;">{{ $booking->guest_name }}</div>
                                                <div class="text-muted extra-small">{{ $booking->guest_email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-muted extra-small">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }} Nights
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 0) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClasses = [
                                                'confirmed' => 'bg-success-subtle text-success border-success-subtle',
                                                'checked_in' => 'bg-info-subtle text-info border-info-subtle',
                                                'checked_out' => 'bg-primary-subtle text-primary border-primary-subtle',
                                                'cancelled' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'pending' => 'bg-warning-subtle text-warning border-warning-subtle'
                                            ];
                                            $currentClass = $statusClasses[$booking->status] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge rounded-pill border {{ $currentClass }} px-3 py-2" style="font-weight: 600; font-size: 0.7rem;">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $completedPayments = $booking->payments->where('status', 'completed')->sum('amount');
                                            $isFullyPaid = $completedPayments >= $booking->final_amount;
                                        @endphp
                                        @if($completedPayments > 0)
                                            <div class="badge bg-success-subtle text-success border-0 px-2 py-1">
                                                {{ $isFullyPaid ? 'Paid' : 'Partial' }}
                                            </div>
                                        @else
                                            <div class="badge bg-light text-muted border px-2 py-1">Unpaid</div>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(auth()->user()->hasPermission('bookings.view'))
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-action btn-view" title="View Booking">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('bookings.edit'))
                                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn-action btn-edit" title="Edit Booking">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                                </a>
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('bookings.delete'))
                                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete" title="Delete Booking">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top-0 py-4">
                    <div class="d-flex justify-content-center">
                        {{ $bookings->links() }}
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <h5>No Bookings Found</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Premium Palette & Layout */
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-info-subtle { background-color: #e0f2fe !important; color: #0369a1 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #991b1b !important; }
    
    .table td { padding: 1.1rem 0.5rem; }
    .table-hover tbody tr:hover { background-color: #f8fafc !important; }

    /* ACTION BUTTONS DESIGN */
    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        padding: 0;
    }

    .btn-view { background-color: #e0f2fe; color: #0ea5e9; }
    .btn-view:hover { background-color: #0ea5e9; color: white; transform: translateY(-2px); }

    .btn-edit { background-color: #fef3c7; color: #d97706; }
    .btn-edit:hover { background-color: #d97706; color: white; transform: translateY(-2px); }

    .btn-delete { background-color: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background-color: #dc2626; color: white; transform: translateY(-2px); }

    /* Fix for shadow and focus */
    .shadow-none:focus { box-shadow: none !important; border-color: #cbd5e1 !important; }
</style>
@endsection