@extends('layouts.admin')

@section('title', 'Room Type Details')
@section('page-title', $roomType->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('admin.room_type_information') }}</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th width="200">{{ __('admin.name') }}:</th>
                        <td><strong>{{ $roomType->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.description') }}:</th>
                        <td>{{ $roomType->description ?? __('admin.n_a') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.price_per_night_label') }}:</th>
                        <td><strong class="text-success">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($roomType->price_per_night, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.max_guests') }}:</th>
                        <td>{{ $roomType->max_guests }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.max_adults') }}:</th>
                        <td>{{ $roomType->max_adults }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.max_children') }}:</th>
                        <td>{{ $roomType->max_children }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.total_rooms') }}:</th>
                        <td>{{ $roomType->total_rooms }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.status') }}:</th>
                        <td>
                            <span class="badge bg-{{ $roomType->is_active ? 'success' : 'secondary' }}">
                                {{ $roomType->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('admin.created_at') }}:</th>
                        <td>{{ $roomType->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($roomType->amenities->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('admin.amenities_list') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($roomType->amenities as $amenity)
                        <div class="col-md-3 mb-2">
                            @if($amenity->icon)
                                <i class="bi {{ $amenity->icon }}"></i>
                            @endif
                            {{ $amenity->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($roomType->images->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('admin.images_list') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($roomType->images as $image)
                        <div class="col-md-3 mb-3">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 class="img-fluid rounded" 
                                 alt="Room Image"
                                 style="height: 200px; width: 100%; object-fit: cover;">
                            @if($image->is_primary)
                                <small class="text-primary">{{ __('admin.primary_image') }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($roomType->rooms->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5>{{ __('admin.rooms_list') }} ({{ $roomType->rooms->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('admin.room_number') }}</th>
                                <th>{{ __('admin.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomType->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('admin.actions') }}</h5>
            </div>
            <div class="card-body">
                @if(auth()->user()->hasPermission('rooms.edit'))
                    <a href="{{ route('admin.rooms.edit', $roomType->id) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> {{ __('admin.edit_room_type') }}
                    </a>
                @endif
                @if(auth()->user()->hasPermission('rooms.delete'))
                    <form action="{{ route('admin.rooms.destroy', $roomType->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" data-message="{{ __('admin.confirm_proceed') }}">
                            <i class="bi bi-trash"></i> {{ __('admin.delete_room_type') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
