<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStatusChanged;

class PayPalController extends Controller
{
    protected PayPalService $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Show the payment page for a booking
     */
    public function showPayment($bookingId)
    {
        $booking = Booking::with(['details.roomType', 'payments'])
            ->where('id', $bookingId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        // Calculate remaining balance
        $paidAmount = $booking->payments()->where('status', 'completed')->sum('amount');
        $remainingBalance = $booking->final_amount - $paidAmount;

        if ($remainingBalance <= 0) {
            return redirect()->route('booking.confirmation', $booking->id)
                ->with('info', 'This booking has already been fully paid.');
        }

        return view('public.booking.payment', [
            'booking' => $booking,
            'remainingBalance' => $remainingBalance,
            'paidAmount' => $paidAmount,
            'paypalClientId' => $this->paypalService->getClientId(),
            'paypalCurrency' => $this->paypalService->getCurrency(),
            'stripePublicKey' => config('services.stripe.public_key'),
        ]);
    }

    /**
     * Create a PayPal order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Calculate remaining balance
        $paidAmount = $booking->payments()->where('status', 'completed')->sum('amount');
        $remainingBalance = $booking->final_amount - $paidAmount;

        // Validate amount doesn't exceed remaining balance
        $amount = min($request->amount, $remainingBalance);

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment amount or booking already paid.',
            ], 400);
        }

        $order = $this->paypalService->createOrder(
            $amount,
            $booking->booking_number,
            "Hotel booking payment for {$booking->booking_number}"
        );

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create PayPal order. Please try again.',
            ], 500);
        }

        // Store order ID in session for verification
        session([
            'paypal_order_id' => $order['id'],
            'paypal_booking_id' => $booking->id,
            'paypal_amount' => $amount,
        ]);

        return response()->json([
            'success' => true,
            'orderID' => $order['id'],
        ]);
    }

    /**
     * Capture the PayPal order after approval
     */
    public function captureOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $orderId = $request->order_id;
        $sessionOrderId = session('paypal_order_id');
        $bookingId = session('paypal_booking_id');
        $amount = session('paypal_amount');

        // Verify the order ID matches
        if ($orderId !== $sessionOrderId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order. Please try again.',
            ], 400);
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $result = $this->paypalService->captureOrder($orderId);

        if (!$result || $result['status'] !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Payment capture failed. Please try again.',
            ], 500);
        }

        DB::beginTransaction();

        try {
            // Extract transaction details
            $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? null;
            $transactionId = $capture['id'] ?? $orderId;

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => 'completed',
                'payment_method' => 'paypal',
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'notes' => 'PayPal payment - Order ID: ' . $orderId,
                'paid_at' => now(),
            ]);

            // If pending and payment received, confirm booking
            if ($booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
                
                // Send confirmation email
                try {
                    Mail::to($booking->guest_email)->send(
                        new BookingStatusChanged($booking, 'Your payment has been received and your booking is now confirmed.')
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
                }
            }

            // Clear session data
            session()->forget(['paypal_order_id', 'paypal_booking_id', 'paypal_amount']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully!',
                'redirect' => route('booking.confirmation', $booking->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PayPal payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment.',
            ], 500);
        }
    }

    /**
     * Handle PayPal return (redirect flow)
     */
    public function capture(Request $request)
    {
        $token = $request->query('token');
        $bookingId = session('paypal_booking_id');

        if (!$token || !$bookingId) {
            return redirect()->route('home')
                ->with('error', 'Invalid payment session. Please try again.');
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return redirect()->route('home')
                ->with('error', 'Booking not found.');
        }

        return redirect()->route('paypal.payment', $booking->id)
            ->with('paypal_token', $token);
    }

    /**
     * Handle PayPal cancellation
     */
    public function cancel(Request $request)
    {
        $bookingId = session('paypal_booking_id');
        
        // Clear session data
        session()->forget(['paypal_order_id', 'paypal_booking_id', 'paypal_amount']);

        if ($bookingId) {
            return redirect()->route('paypal.payment', $bookingId)
                ->with('warning', 'Payment was cancelled. You can try again when ready.');
        }

        return redirect()->route('home')
            ->with('warning', 'Payment was cancelled.');
    }

    /**
     * Process refund for a PayPal payment (Admin only)
     */
    public function refund(Request $request, $paymentId)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        $payment = Payment::with('booking')->findOrFail($paymentId);

        // Verify payment is PayPal and completed
        if ($payment->payment_method !== 'paypal') {
            return redirect()->back()
                ->with('error', 'This payment was not made via PayPal.');
        }

        if ($payment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Only completed payments can be refunded.');
        }

        if (!$payment->transaction_id) {
            return redirect()->back()
                ->with('error', 'No transaction ID found for this payment.');
        }

        $refundAmount = $request->amount ?? $payment->amount;

        // Cannot refund more than the original amount
        if ($refundAmount > $payment->amount) {
            return redirect()->back()
                ->with('error', 'Refund amount cannot exceed the original payment amount.');
        }

        $result = $this->paypalService->refundPayment($payment->transaction_id, $refundAmount);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Failed to process refund. Please try again or contact PayPal support.');
        }

        DB::beginTransaction();

        try {
            // Create refund payment record
            Payment::create([
                'booking_id' => $payment->booking_id,
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => 'refunded',
                'payment_method' => 'paypal',
                'amount' => -$refundAmount, // Negative amount for refund
                'transaction_id' => $result['id'] ?? null,
                'notes' => 'PayPal refund for payment ' . $payment->payment_number . ($request->reason ? '. Reason: ' . $request->reason : ''),
                'paid_at' => now(),
            ]);

            // Update original payment status if full refund
            if ($refundAmount >= $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            // Send refund notification email
            try {
                Mail::to($payment->booking->guest_email)->send(
                    new BookingStatusChanged($payment->booking, 'A refund of ' . config('services.paypal.currency', 'USD') . ' ' . number_format($refundAmount, 2) . ' has been processed for your booking.')
                );
            } catch (\Exception $e) {
                Log::error('Failed to send refund email: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Refund of ' . number_format($refundAmount, 2) . ' processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while processing the refund.');
        }
    }

    /**
     * Get payment status via AJAX
     */
    public function getPaymentStatus($bookingId)
    {
        $booking = Booking::with('payments')->findOrFail($bookingId);
        
        $paidAmount = $booking->payments()->where('status', 'completed')->sum('amount');
        $refundedAmount = abs($booking->payments()->where('status', 'refunded')->sum('amount'));
        $netPaid = $paidAmount - $refundedAmount;
        $remainingBalance = $booking->final_amount - $netPaid;

        return response()->json([
            'booking_number' => $booking->booking_number,
            'total_amount' => $booking->final_amount,
            'paid_amount' => $paidAmount,
            'refunded_amount' => $refundedAmount,
            'net_paid' => $netPaid,
            'remaining_balance' => max(0, $remainingBalance),
            'is_fully_paid' => $remainingBalance <= 0,
            'status' => $booking->status,
        ]);
    }
}
