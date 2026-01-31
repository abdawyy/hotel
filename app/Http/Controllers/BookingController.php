<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStatusChanged;

class BookingController extends Controller
{
    /**
     * Show the booking form for a specific room type.
     */
    public function create(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $roomType = RoomType::with(['amenities', 'images'])->findOrFail($request->room_type_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Check availability
        $availableCount = $roomType->getAvailableRoomsCount($checkIn, $checkOut);

        if ($availableCount <= 0) {
            return redirect()->route('rooms.index')
                ->with('error', 'Sorry, this room type is not available for the selected dates.');
        }

        // Calculate pricing
        $pricePerNight = $roomType->price_per_night;
        $subtotal = $pricePerNight * $nights;
        $taxRate = (float) Setting::getValue('tax_rate', 10); // Default 10%
        $taxAmount = ($subtotal * $taxRate) / 100;
        $totalPrice = $subtotal + $taxAmount;

        return view('public.booking.create', compact(
            'roomType',
            'checkIn',
            'checkOut',
            'nights',
            'availableCount',
            'pricePerNight',
            'subtotal',
            'taxRate',
            'taxAmount',
            'totalPrice'
        ));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        $roomType = RoomType::findOrFail($validated['room_type_id']);
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);

        // Check availability again
        $availableCount = $roomType->getAvailableRoomsCount($checkIn, $checkOut);
        if ($availableCount <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sorry, this room type is no longer available for the selected dates.');
        }

        // Calculate pricing
        $pricePerNight = $roomType->price_per_night;
        $subtotal = $pricePerNight * $nights;
        $taxRate = (float) Setting::getValue('tax_rate', 10);
        $taxAmount = ($subtotal * $taxRate) / 100;
        $discountAmount = 0; // Can be added later for promo codes
        $totalPrice = $subtotal + $taxAmount - $discountAmount;

        DB::beginTransaction();

        try {
            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'booking_number' => Booking::generateBookingNumber(),
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'status' => 'pending',
                'total_price' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $totalPrice,
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
                'guest_address' => $validated['guest_address'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            // Send booking created email
            try {
                Mail::to($booking->guest_email)->send(new BookingStatusChanged($booking, 'Your booking has been created and is pending confirmation.'));
            } catch (\Exception $e) {}

            // Find and assign available room
            $availableRoom = Room::available()
                ->ofType($roomType->id)
                ->whereDoesntHave('bookingDetails.booking', function($query) use ($checkIn, $checkOut) {
                    $query->where(function($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function($q2) use ($checkIn, $checkOut) {
                              $q2->where('check_in_date', '<=', $checkIn)
                                 ->where('check_out_date', '>=', $checkOut);
                          });
                    })
                    ->whereIn('status', ['confirmed', 'checked_in']);
                })
                ->first();

            // Create booking detail
            BookingDetail::create([
                'booking_id' => $booking->id,
                'room_type_id' => $roomType->id,
                'room_id' => $availableRoom?->id,
                'quantity' => 1,
                'price_per_night' => $pricePerNight,
                'nights' => $nights,
                'subtotal' => $subtotal,
            ]);

            // Mark room as reserved if assigned
            if ($availableRoom) {
                $availableRoom->update(['status' => 'reserved']);
            }

            DB::commit();

            return redirect()->route('booking.confirmation', $booking->id)
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing your booking. Please try again.');
        }
    }

    /**
     * Display booking confirmation page.
     */
    public function confirmation($id)
    {
        $booking = Booking::with(['details.roomType.images', 'details.roomType.primaryImage', 'details.roomType.amenities', 'details.room', 'user'])
            ->where('id', $id)
            ->where(function($query) {
                // If user is authenticated, check if they own the booking
                if (Auth::check()) {
                    $query->where('user_id', Auth::id())
                          ->orWhere('guest_email', Auth::user()->email);
                }
            })
            ->firstOrFail();

        return view('public.booking.confirmation', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        // For confirmed bookings, check if cancellation is allowed (48 hours before check-in)
        // For pending bookings, cancellation is always allowed
        if ($booking->status === 'confirmed') {
            $checkInDate = Carbon::parse($booking->check_in_date);
            $now = Carbon::now();
            $hoursUntilCheckIn = $now->diffInHours($checkInDate, false);

            if ($hoursUntilCheckIn < 48) {
                return redirect()->back()
                    ->with('error', 'Cancellation is not allowed within 48 hours of check-in. Please contact support for assistance.');
            }
        }

        DB::beginTransaction();

        try {
            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Cancelled by customer',
            ]);

            // Release assigned room
            foreach ($booking->details as $detail) {
                if ($detail->room) {
                    $detail->room->update(['status' => 'available']);
                }
            }

            DB::commit();

            // Send booking cancelled email
            try {
                Mail::to($booking->guest_email)->send(new BookingStatusChanged($booking, 'Your booking has been cancelled.'));
            } catch (\Exception $e) {}
            return redirect()->route('user.dashboard')
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while cancelling the booking.');
        }
    }
}
