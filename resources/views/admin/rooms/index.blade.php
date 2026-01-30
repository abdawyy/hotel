@extends('layouts.admin')

@section('title', 'Rooms Management')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Room Management</h3>
            <p class="text-muted small">Manage your property's room categories, pricing, and availability at a glance.</p>
        </div>
        <div class="col-md-6 text-md-end">
            @if(auth()->user()->hasPermission('rooms.create'))
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus me-2"></i> {{ __('admin.add_new_room_type') }}
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-door-open fa-lg"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted small mb-0">Total Types</h6>
                        <h4 class="fw-bold mb-0">{{ $roomTypes->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($roomTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th class="ps-4 border-0 py-3 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.image') }}</th>
                                <th class="border-0 py-3 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.name') }}</th>
                                <th class="border-0 py-3 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.price_per_night') }}</th>
                                <th class="border-0 py-3 text-uppercase text-center" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.capacity') }}</th>
                                <th class="border-0 py-3 text-uppercase text-center" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.inventory') }}</th>
                                <th class="border-0 py-3 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.status') }}</th>
                                <th class="pe-4 border-0 py-3 text-uppercase text-end" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomTypes as $roomType)
                                <tr>
                                    <td class="ps-4">
                                        @php
                                            $displayImage = $roomType->primaryImage ?? $roomType->images->first();
                                        @endphp
                                        @if($displayImage)
                                            <img src="{{ asset('storage/' . $displayImage->image_path) }}" 
                                                 alt="{{ $roomType->name }}" 
                                                 class="rounded-3 shadow-sm" 
                                                 style="width: 65px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded-3 shadow-sm border" style="width: 65px; height: 45px;">
                                                <i class="fas fa-image text-muted opacity-50"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $roomType->name }}</h6>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @forelse($roomType->amenities->take(2) as $amenity)
                                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 9px;">{{ $amenity->name }}</span>
                                            @empty
                                                <small class="text-muted italic" style="font-size: 10px;">No amenities</small>
                                            @endforelse
                                            @if($roomType->amenities->count() > 2)
                                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 9px;">+{{ $roomType->amenities->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($roomType->price_per_night, 0) }}</span>
                                        <small class="text-muted d-block" style="font-size: 11px;">/ night</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center px-2 py-1 bg-light rounded text-muted small">
                                            <i class="fas fa-users me-1" style="font-size: 10px;"></i> {{ $roomType->max_guests }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-dark fw-bold px-3 py-1">{{ $roomType->total_rooms }} Rooms</span>
                                    </td>
                                    <td>
                                        @if($roomType->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                                <i class="fas fa-circle me-1" style="font-size: 7px; vertical-align: middle;"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(auth()->user()->hasPermission('rooms.edit'))
                                                <a href="{{ route('admin.rooms.edit', $roomType->id) }}" 
                                                   class="btn btn-sm shadow-sm d-flex align-items-center justify-content-center" 
                                                   style="width: 34px; height: 34px; border-radius: 8px; background: #e0f2fe; color: #0369a1; border: none;" 
                                                   data-bs-toggle="tooltip" title="Edit Room">
                                                    <i class="fas fa-edit small"></i>
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('rooms.delete'))
                                                <form action="{{ route('admin.rooms.destroy', $roomType->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm shadow-sm d-flex align-items-center justify-content-center" 
                                                            style="width: 34px; height: 34px; border-radius: 8px; background: #fee2e2; color: #b91c1c; border: none;" 
                                                            data-message="{{ __('admin.confirm_proceed') }}">
                                                        <i class="fas fa-trash-alt small"></i>
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
                
                <div class="bg-white border-top py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <p class="text-muted small mb-0">Showing {{ $roomTypes->firstItem() }} to {{ $roomTypes->lastItem() }} of {{ $roomTypes->total() }} room types</p>
                        <div>
                            {{ $roomTypes->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="fas fa-door-closed fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-dark fw-bold">{{ __('admin.no_room_types_found') }}</h5>
                    <p class="text-muted">You haven't added any room types yet. Start by creating one.</p>
                    @if(auth()->user()->hasPermission('rooms.create'))
                        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary rounded-pill px-4 mt-2 shadow-sm">
                            {{ __('admin.create_first_room_type') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Styling variables */
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.12) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
    
    .rounded-4 { border-radius: 1rem !important; }
    
    /* Table styling */
    .table > :not(caption) > * > * { padding: 1.1rem 0.75rem; border-bottom-color: #f1f4f9; }
    .table-hover tbody tr:hover { background-color: #f9fbff !important; }
    
    /* Smooth button transitions */
    .btn { transition: all 0.2s ease-in-out; }
    .btn:hover { transform: translateY(-2px); filter: brightness(0.95); }
    
    /* Pagination styling */
    .pagination { margin-bottom: 0; gap: 5px; }
    .page-item .page-link { border: none; border-radius: 8px !important; color: #4b5563; font-weight: 500; }
    .page-item.active .page-link { background-color: #0d6efd; color: white; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25); }
</style>

@push('scripts')
<script>
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection