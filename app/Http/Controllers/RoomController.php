<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Room;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    /**
     * Display a listing of available rooms.
     */
    public function index(Request $request)
    {
        $query = RoomType::active()->with(['primaryImage', 'images', 'amenities']);

        // Parse check-in and check-out dates
        $checkIn = null;
        $checkOut = null;
        $hasDateFilter = false;

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);
            $hasDateFilter = true;
        }

        // Filter by max guests
        if ($request->filled('adults')) {
            $adults = (int) $request->adults;
            $query->where('max_adults', '>=', $adults);
        }

        if ($request->filled('children')) {
            $children = (int) $request->children;
            $query->where('max_children', '>=', $children);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Filter by availability if dates are provided
        if ($hasDateFilter) {
            $query->availableBetween($checkIn, $checkOut);
        }

        $rooms = $query->orderBy('price_per_night', 'asc')->paginate(12);

        // Calculate available count for each room when dates are provided
        if ($hasDateFilter) {
            $rooms->getCollection()->transform(function ($room) use ($checkIn, $checkOut) {
                $room->available_count = $room->getAvailableRoomsCount($checkIn, $checkOut);
                return $room;
            });
        }

        $amenities = Amenity::orderBy('name')->get();

        return view('public.rooms.index', compact('rooms', 'amenities', 'checkIn', 'checkOut', 'hasDateFilter'));
    }

    /**
     * Display the specified room type details.
     */
    public function show($id)
    {
        $roomType = RoomType::with(['images' => function($query) {
            $query->orderBy('display_order')->orderBy('id');
        }, 'amenities'])
        ->findOrFail($id);

        // Get similar room types (same price range or category)
        $similarRooms = RoomType::active()
            ->where('id', '!=', $id)
            ->whereBetween('price_per_night', [
                $roomType->price_per_night * 0.8,
                $roomType->price_per_night * 1.2
            ])
            ->with(['primaryImage', 'images'])
            ->take(4)
            ->get();

        // Dates with zero availability (for disabling reserved days in calendar)
        $today = Carbon::today();
        $rangeEnd = $today->copy()->addYear();
        $unavailableDates = $roomType->getUnavailableDates($today, $rangeEnd);

        return view('public.rooms.show', compact('roomType', 'similarRooms', 'unavailableDates'));
    }

    /**
     * Check room availability for given dates.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $roomType = RoomType::findOrFail($request->room_type_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $availableCount = $roomType->getAvailableRoomsCount($checkIn, $checkOut);

        return response()->json([
            'available' => $availableCount > 0,
            'available_count' => $availableCount,
            'room_type' => $roomType->name,
        ]);
    }
}
