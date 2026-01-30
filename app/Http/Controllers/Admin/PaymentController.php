<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use ChecksPermissions;
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('payments.view');
        
        $query = Payment::with('booking.user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by payment number or transaction ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $this->authorizePermission('payments.create');
        
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments.create', compact('bookings'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('payments.create');
        
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,online',
            'status' => 'required|in:pending,completed,failed',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $booking = Booking::findOrFail($validated['booking_id']);

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

            return redirect()->route('admin.payments.show', $payment->id)
                ->with('success', 'Payment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the payment.');
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $this->authorizePermission('payments.view');
        
        $payment = Payment::with(['booking.user', 'booking.details.roomType.images', 'booking.details.roomType.primaryImage'])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit($id)
    {
        $this->authorizePermission('payments.edit');
        
        $payment = Payment::with('booking')->findOrFail($id);
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments.edit', compact('payment', 'bookings'));
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, $id)
    {
        $this->authorizePermission('payments.edit');
        
        $payment = Payment::with('booking')->findOrFail($id);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,online',
            'status' => 'required|in:pending,completed,failed',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $payment->status;
            $booking = Booking::findOrFail($validated['booking_id']);

            $payment->update([
                'booking_id' => $booking->id,
                'status' => $validated['status'],
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'paid_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

            // If payment status changed to completed, confirm booking if it's pending
            if ($validated['status'] === 'completed' && $oldStatus !== 'completed' && $booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
            }

            DB::commit();

            return redirect()->route('admin.payments.show', $payment->id)
                ->with('success', 'Payment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the payment.');
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        $this->authorizePermission('payments.delete');
        
        $payment = Payment::with('booking')->findOrFail($id);

        // Prevent deletion of completed payments
        if ($payment->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot delete completed payments.');
        }

        DB::beginTransaction();

        try {
            $payment->delete();

            DB::commit();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the payment.');
        }
    }
}
