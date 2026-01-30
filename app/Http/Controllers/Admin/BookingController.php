<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use App\Models\Payment;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    use ChecksPermissions;
    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $this->authorizePermission('bookings.create');
        
        $roomTypes = RoomType::where('is_active', true)->with(['images', 'primaryImage'])->orderBy('name')->get();
        $customers = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->orderBy('name')->get();
        
        return view('admin.bookings.create', compact('roomTypes', 'customers'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('bookings.create');
        
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'guest_address' => 'nullable|string',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'room_types' => 'required|array|min:1',
            'room_types.*.room_type_id' => 'required|exists:room_types,id',
            'room_types.*.quantity' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Check availability for all room types
            foreach ($validated['room_types'] as $roomTypeData) {
                $roomType = RoomType::findOrFail($roomTypeData['room_type_id']);
                $availableCount = $roomType->getAvailableRoomsCount(
                    $validated['check_in_date'],
                    $validated['check_out_date']
                );
                
                if ($availableCount < $roomTypeData['quantity']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Not enough available rooms for {$roomType->name}. Available: {$availableCount}, Requested: {$roomTypeData['quantity']}");
                }
            }

            // Calculate nights
            $nights = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));

            // Calculate totals first
            $totalPrice = 0;
            foreach ($validated['room_types'] as $roomTypeData) {
                $roomType = RoomType::findOrFail($roomTypeData['room_type_id']);
                $subtotal = $roomType->price_per_night * $roomTypeData['quantity'] * $nights;
                $totalPrice += $subtotal;
            }

            // Calculate tax and final amount
            $taxRate = Setting::getValue('tax_rate', 10) / 100;
            $taxAmount = $totalPrice * $taxRate;
            $finalAmount = $totalPrice + $taxAmount;

            // Create booking with all required fields
            $booking = Booking::create([
                'user_id' => $validated['user_id'] ?? null,
                'booking_number' => Booking::generateBookingNumber(),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'status' => $validated['status'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'] ?? null,
                'guest_address' => $validated['guest_address'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
                'total_price' => $totalPrice,
                'tax_amount' => $taxAmount,
                'final_amount' => $finalAmount,
            ]);

            // Create booking details
            foreach ($validated['room_types'] as $roomTypeData) {
                $roomType = RoomType::findOrFail($roomTypeData['room_type_id']);
                $subtotal = $roomType->price_per_night * $roomTypeData['quantity'] * $nights;
                
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'room_type_id' => $roomType->id,
                    'quantity' => $roomTypeData['quantity'],
                    'price_per_night' => $roomType->price_per_night,
                    'nights' => $nights,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', 'Booking created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the booking: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('bookings.view');
        
        $query = Booking::with(['user', 'details.roomType.images', 'details.roomType.primaryImage', 'details.room', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('check_in_from')) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }

        if ($request->filled('check_in_to')) {
            $query->where('check_in_date', '<=', $request->check_in_to);
        }

        // Search by booking number or guest name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking.
     */
    public function show($id)
    {
        $this->authorizePermission('bookings.view');
        
        $booking = Booking::with([
            'user',
            'details.roomType.images',
            'details.roomType.primaryImage',
            'details.room',
            'payments'
        ])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit($id)
    {
        $this->authorizePermission('bookings.edit');
        
        $booking = Booking::with(['details.roomType.images', 'details.roomType.primaryImage', 'user'])->findOrFail($id);
        $roomTypes = RoomType::where('is_active', true)->with(['images', 'primaryImage'])->orderBy('name')->get();
        $customers = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->orderBy('name')->get();

        return view('admin.bookings.edit', compact('booking', 'roomTypes', 'customers'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $id)
    {
        $this->authorizePermission('bookings.edit');
        
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'guest_address' => 'nullable|string',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'room_types' => 'required|array|min:1',
            'room_types.*.room_type_id' => 'required|exists:room_types,id',
            'room_types.*.quantity' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        DB::beginTransaction();

        try {
            // Check availability if dates or room types changed
            if ($booking->check_in_date != $validated['check_in_date'] || 
                $booking->check_out_date != $validated['check_out_date']) {
                foreach ($validated['room_types'] as $roomTypeData) {
                    $roomType = RoomType::findOrFail($roomTypeData['room_type_id']);
                    $availableCount = $roomType->getAvailableRoomsCount(
                        $validated['check_in_date'],
                        $validated['check_out_date']
                    );
                    
                    // Exclude current booking's rooms from count
                    $currentBookingRooms = $booking->details()
                        ->where('room_type_id', $roomType->id)
                        ->sum('quantity');
                    $availableCount += $currentBookingRooms;
                    
                    if ($availableCount < $roomTypeData['quantity']) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', "Not enough available rooms for {$roomType->name}. Available: {$availableCount}, Requested: {$roomTypeData['quantity']}");
                    }
                }
            }

            // Calculate nights
            $nights = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));

            // Update booking
            $booking->update([
                'user_id' => $validated['user_id'] ?? null,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'status' => $validated['status'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'] ?? null,
                'guest_address' => $validated['guest_address'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            // Delete old booking details
            $booking->details()->delete();

            // Create new booking details
            $totalPrice = 0;
            foreach ($validated['room_types'] as $roomTypeData) {
                $roomType = RoomType::findOrFail($roomTypeData['room_type_id']);
                $subtotal = $roomType->price_per_night * $roomTypeData['quantity'] * $nights;
                
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'room_type_id' => $roomType->id,
                    'quantity' => $roomTypeData['quantity'],
                    'price_per_night' => $roomType->price_per_night,
                    'nights' => $nights,
                    'subtotal' => $subtotal,
                ]);
                
                $totalPrice += $subtotal;
            }

            // Calculate tax and final amount
            $taxRate = Setting::getValue('tax_rate', 10) / 100;
            $taxAmount = $totalPrice * $taxRate;
            $finalAmount = $totalPrice + $taxAmount;

            $booking->update([
                'total_price' => $totalPrice,
                'tax_amount' => $taxAmount,
                'final_amount' => $finalAmount,
            ]);

            DB::commit();

            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', 'Booking updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the booking: ' . $e->getMessage());
        }
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorizePermission('bookings.manage-status');
        
        $booking = Booking::findOrFail($id);

        // Prevent status changes if booking is already cancelled
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot change status of a cancelled booking.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $booking = Booking::with('details.room')->findOrFail($id);
        $oldStatus = $booking->status;

        DB::beginTransaction();

        try {
            $booking->update(['status' => $validated['status']]);

            // Handle room status based on booking status
            if ($validated['status'] === 'checked_in') {
                foreach ($booking->details as $detail) {
                    if ($detail->room) {
                        $detail->room->update(['status' => 'occupied']);
                    }
                }
            } elseif ($validated['status'] === 'checked_out') {
                foreach ($booking->details as $detail) {
                    if ($detail->room) {
                        $detail->room->update(['status' => 'available']);
                    }
                }
            } elseif ($validated['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
                $booking->update([
                    'cancelled_at' => now(),
                    'cancellation_reason' => $request->cancellation_reason ?? 'Cancelled by admin',
                ]);
                
                // Release rooms
                foreach ($booking->details as $detail) {
                    if ($detail->room) {
                        $detail->room->update(['status' => 'available']);
                    }
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Booking status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating booking status.');
        }
    }

    /**
     * Assign room to booking detail.
     */
    public function assignRoom(Request $request, $bookingDetailId)
    {
        $this->authorizePermission('bookings.edit');
        
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $bookingDetail = BookingDetail::with('booking')->findOrFail($bookingDetailId);
        $room = Room::findOrFail($validated['room_id']);

        // Check if room is available
        if ($room->status !== 'available') {
            return redirect()->back()
                ->with('error', 'Selected room is not available.');
        }

        // Release old room if assigned
        if ($bookingDetail->room_id) {
            $oldRoom = Room::find($bookingDetail->room_id);
            if ($oldRoom) {
                $oldRoom->update(['status' => 'available']);
            }
        }

        // Assign new room
        $bookingDetail->update(['room_id' => $validated['room_id']]);
        $room->update(['status' => 'reserved']);

        return redirect()->back()
            ->with('success', 'Room assigned successfully.');
    }

    /**
     * Create payment for booking.
     */
    public function createPayment(Request $request, $id)
    {
        $this->authorizePermission('bookings.edit');
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,online',
            'status' => 'required|in:pending,completed,failed',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => $validated['status'],
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'paid_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            // If payment is completed, confirm booking if it's pending
            if ($validated['status'] === 'completed' && $booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Payment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating payment.');
        }
    }

    /**
     * Cancel booking.
     */
    public function cancel($id)
    {
        $this->authorizePermission('bookings.manage-status');
        
        $booking = Booking::with('details.room')->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Booking is already cancelled.');
        }

        DB::beginTransaction();

        try {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => request('cancellation_reason', 'Cancelled by admin'),
            ]);

            // Release rooms
            foreach ($booking->details as $detail) {
                if ($detail->room) {
                    $detail->room->update(['status' => 'available']);
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while cancelling the booking.');
        }
    }

    /**
     * Remove the specified booking.
     */
    public function destroy($id)
    {
        $this->authorizePermission('bookings.delete');
        
        $booking = Booking::with(['details.room', 'payments'])->findOrFail($id);

        // Check if booking has payments
        if ($booking->payments->where('status', 'completed')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete booking with completed payments.');
        }

        DB::beginTransaction();

        try {
            // Release rooms
            foreach ($booking->details as $detail) {
                if ($detail->room) {
                    $detail->room->update(['status' => 'available']);
                }
            }

            // Delete payments
            $booking->payments()->delete();

            // Delete booking details
            $booking->details()->delete();

            // Delete booking
            $booking->delete();

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the booking.');
        }
    }
}
