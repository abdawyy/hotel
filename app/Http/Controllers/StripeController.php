<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStatusChanged;

class StripeController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a Stripe Payment Intent
     */
    public function createPaymentIntent(Request $request)
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

        $paymentIntent = $this->stripeService->createPaymentIntent(
            $amount,
            $booking->booking_number,
            ['booking_id' => $booking->id]
        );

        if (!$paymentIntent) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.',
            ], 500);
        }

        // Store payment intent ID in session for verification
        session([
            'stripe_payment_intent_id' => $paymentIntent['id'],
            'stripe_booking_id' => $booking->id,
            'stripe_amount' => $amount,
        ]);

        return response()->json([
            'success' => true,
            'clientSecret' => $paymentIntent['client_secret'],
            'paymentIntentId' => $paymentIntent['id'],
        ]);
    }

    /**
     * Confirm payment after successful Stripe payment
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $paymentIntentId = $request->payment_intent_id;
        $sessionPaymentIntentId = session('stripe_payment_intent_id');
        $bookingId = session('stripe_booking_id');
        $amount = session('stripe_amount');

        // Verify the payment intent ID matches
        if ($paymentIntentId !== $sessionPaymentIntentId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment. Please try again.',
            ], 400);
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        // Verify payment status with Stripe
        $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

        if (!$paymentIntent || $paymentIntent['status'] !== 'succeeded') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not completed. Please try again.',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => 'completed',
                'payment_method' => 'stripe',
                'amount' => $amount,
                'transaction_id' => $paymentIntentId,
                'notes' => 'Stripe card payment - Payment Intent: ' . $paymentIntentId,
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
            session()->forget(['stripe_payment_intent_id', 'stripe_booking_id', 'stripe_amount']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully!',
                'redirect' => route('booking.confirmation', $booking->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment.',
            ], 500);
        }
    }

    /**
     * Process refund for a Stripe payment (Admin only)
     */
    public function refund(Request $request, $paymentId)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        $payment = Payment::with('booking')->findOrFail($paymentId);

        // Verify payment is Stripe and completed
        if ($payment->payment_method !== 'stripe') {
            return redirect()->back()
                ->with('error', 'This payment was not made via Stripe.');
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

        $result = $this->stripeService->createRefund($payment->transaction_id, $refundAmount);

        if (!$result || $result['status'] !== 'succeeded') {
            return redirect()->back()
                ->with('error', 'Failed to process refund. Please try again or contact Stripe support.');
        }

        DB::beginTransaction();

        try {
            // Create refund payment record
            Payment::create([
                'booking_id' => $payment->booking_id,
                'payment_number' => Payment::generatePaymentNumber(),
                'status' => 'refunded',
                'payment_method' => 'stripe',
                'amount' => -$refundAmount,
                'transaction_id' => $result['id'] ?? null,
                'notes' => 'Stripe refund for payment ' . $payment->payment_number . ($request->reason ? '. Reason: ' . $request->reason : ''),
                'paid_at' => now(),
            ]);

            // Update original payment status if full refund
            if ($refundAmount >= $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            // Send refund notification email
            try {
                Mail::to($payment->booking->guest_email)->send(
                    new BookingStatusChanged($payment->booking, 'A refund of ' . strtoupper(config('services.stripe.currency', 'usd')) . ' ' . number_format($refundAmount, 2) . ' has been processed for your booking.')
                );
            } catch (\Exception $e) {
                Log::error('Failed to send refund email: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Refund of ' . number_format($refundAmount, 2) . ' processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe refund processing error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while processing the refund.');
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (!$this->stripeService->verifyWebhookSignature($payload, $sigHeader)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;
        }

        return response()->json(['received' => true]);
    }

    protected function handlePaymentIntentSucceeded(array $paymentIntent)
    {
        Log::info('Payment Intent Succeeded: ' . $paymentIntent['id']);
    }

    protected function handlePaymentIntentFailed(array $paymentIntent)
    {
        Log::warning('Payment Intent Failed: ' . $paymentIntent['id']);
    }
}
