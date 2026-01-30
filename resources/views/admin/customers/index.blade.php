@extends('layouts.admin')

@section('title', 'Customers Management')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Customer Directory</h3>
            <p class="text-muted small mb-0">Manage guest profiles and review loyalty history.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if(auth()->user()->hasPermission('customers.create'))
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary rounded-3 px-4 shadow-sm fw-bold">
                    Add New Customer
                </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label class="form-label small fw-bold text-muted text-uppercase">Search Guest</label>
                        <input type="text" name="search" class="form-control border-light-subtle rounded-3 shadow-none" 
                               placeholder="Search by Name, Email, or Phone..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold">Search</button>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Search">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 4v6h-6"></path>
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4 border-0 py-3">Guest Info</th>
                                <th class="border-0">Contact</th>
                                <th class="border-0 text-center">Bookings</th>
                                <th class="border-0">Total Spent</th>
                                <th class="pe-4 border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                                <div class="text-muted extra-small">ID: #{{ $customer->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-500">{{ $customer->email }}</div>
                                        <div class="small text-muted">{{ $customer->phone ?? '---' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3">
                                            {{ $customer->bookings->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $totalSpent = $customer->bookings()
                                                ->join('payments', 'bookings.id', '=', 'payments.booking_id')
                                                ->where('payments.status', 'completed')
                                                ->sum('payments.amount');
                                        @endphp
                                        <div class="fw-bold text-dark">
                                            {{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($totalSpent, 2) }}
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(auth()->user()->hasPermission('customers.view'))
                                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-action btn-view">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </a>
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('customers.edit'))
                                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-action btn-edit">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                                                </a>
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('customers.delete'))
                                                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
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
            @else
                <div class="p-5 text-center text-muted">No Records Found.</div>
            @endif
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .extra-small { font-size: 0.7rem; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; color: #0d6efd; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; color: #0dcaf0; }
    .table td { padding: 1.1rem 0.5rem; }

    /* ACTION BUTTON STYLING */
    .btn-action {
        width: 38px;
        height: 38px;
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
    
    .btn-action svg { stroke-width: 2.5px; }

    /* Fix for shadow and focus */
    .shadow-none:focus { box-shadow: none !important; border-color: #cbd5e1 !important; }
</style>
@endsection