@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">Dashboard Overview</h3>
            <p class="text-muted small">Welcome back, {{ auth()->user()->name }}. Here is what's happening today.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @if(isset($canViewBookings) && $canViewBookings)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="small fw-bold text-muted text-uppercase mb-1">{{ __('admin.total_bookings') }}</p>
                                <h2 class="fw-bold mb-0 text-dark">{{ $totalBookings }}</h2>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-3 p-3">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small">
                            <span class="text-success fw-bold"><i class="bi bi-arrow-up"></i> +12%</span>
                            <span class="text-muted ms-1">vs last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 border-top border-4 border-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="small fw-bold text-muted text-uppercase mb-1">{{ __('admin.today_check_ins') }}</p>
                                <h2 class="fw-bold mb-0 text-dark">{{ $todayCheckIns }}</h2>
                            </div>
                            <div class="bg-success-subtle text-success rounded-3 p-3">
                                <i class="bi bi-box-arrow-in-right fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 border-top border-4 border-info">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="small fw-bold text-muted text-uppercase mb-1">{{ __('admin.today_check_outs') }}</p>
                                <h2 class="fw-bold mb-0 text-dark">{{ $todayCheckOuts }}</h2>
                            </div>
                            <div class="bg-info-subtle text-info rounded-3 p-3">
                                <i class="bi bi-box-arrow-left fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($canViewPayments) && $canViewPayments)
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="small fw-bold text-muted text-uppercase mb-1">{{ __('admin.total_revenue') }}</p>
                                <h2 class="fw-bold mb-0 text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($totalRevenue, 0) }}</h2>
                            </div>
                            <div class="bg-warning-subtle text-warning rounded-3 p-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">Estimated earnings</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if((isset($canViewPayments) && $canViewPayments) || (isset($canViewBookings) && $canViewBookings))
        <div class="row g-4 mb-4">
            @if(isset($canViewPayments) && $canViewPayments)
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark mb-0">{{ __('admin.monthly_revenue') }}</h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="revenueChart" height="240"></canvas>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($canViewBookings) && $canViewBookings)
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark mb-0">{{ __('admin.booking_status') }}</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-group list-group-flush border-0">
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center bg-light rounded-3 mb-2 px-3">
                                    <span class="fw-bold text-muted small">{{ __('admin.pending') }}</span>
                                    <span class="badge bg-warning rounded-pill px-3">{{ $pendingBookings }}</span>
                                </div>
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center bg-light rounded-3 mb-2 px-3">
                                    <span class="fw-bold text-muted small">{{ __('admin.confirmed') }}</span>
                                    <span class="badge bg-info rounded-pill px-3">{{ $confirmedBookings }}</span>
                                </div>
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center bg-light rounded-3 mb-2 px-3">
                                    <span class="fw-bold text-muted small">{{ __('admin.checked_in') }}</span>
                                    <span class="badge bg-success rounded-pill px-3">{{ $checkedInBookings }}</span>
                                </div>
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center bg-light rounded-3 px-3">
                                    <span class="fw-bold text-muted small">{{ __('admin.cancelled') }}</span>
                                    <span class="badge bg-danger rounded-pill px-3">{{ $cancelledBookings }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if(isset($canViewBookings) && $canViewBookings)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">{{ __('admin.recent_bookings') }}</h5>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light border fw-bold px-3 rounded-pill">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0 small text-muted text-uppercase">{{ __('admin.booking_number') }}</th>
                                        <th class="py-3 border-0 small text-muted text-uppercase">{{ __('admin.guest') }}</th>
                                        <th class="py-3 border-0 small text-muted text-uppercase text-center">{{ __('admin.status') }}</th>
                                        <th class="px-4 py-3 border-0 small text-muted text-uppercase text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td class="px-4 fw-bold text-primary">{{ $booking->booking_number }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $booking->guest_name }}</div>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $statusClass = [
                                                        'confirmed' => 'bg-info-subtle text-info border-info-subtle',
                                                        'checked_in' => 'bg-success-subtle text-success border-success-subtle',
                                                        'pending' => 'bg-warning-subtle text-warning border-warning-subtle'
                                                    ][$booking->status] ?? 'bg-light text-muted';
                                                @endphp
                                                <span class="badge {{ $statusClass }} border px-3 py-2 rounded-pill small">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 text-end">
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-white border rounded-circle">
                                                    <i class="bi bi-eye text-primary"></i>
                                                </a>
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
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #166534 !important; }
    .bg-info-subtle { background-color: #e0f2fe !important; color: #0369a1 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #92400e !important; }
    
    .table thead th { font-weight: 700; letter-spacing: 0.5px; }
    .card-header h5 { letter-spacing: -0.5px; }
    
    canvas { width: 100% !important; max-height: 250px; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(isset($canViewPayments) && $canViewPayments)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json(collect($monthlyRevenueData)->pluck('month')),
            datasets: [{
                label: 'Revenue',
                data: @json(collect($monthlyRevenueData)->pluck('revenue')),
                borderColor: '#4338ca',
                backgroundColor: (context) => {
                    const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(67, 56, 202, 0.1)');
                    gradient.addColorStop(1, 'rgba(67, 56, 202, 0)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#4338ca'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { borderDash: [5, 5], drawBorder: false }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
</script>
@endpush