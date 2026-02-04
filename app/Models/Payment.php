<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Payment methods constants
     */
    const METHOD_CASH = 'cash';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_ONLINE = 'online';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_STRIPE = 'stripe';

    /**
     * Payment status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'booking_id',
        'payment_number',
        'status',
        'payment_method',
        'amount',
        'transaction_id',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get available payment methods
     */
    public static function getPaymentMethods(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_ONLINE => 'Online',
            self::METHOD_PAYPAL => 'PayPal',
            self::METHOD_STRIPE => 'Stripe (Card)',
        ];
    }

    /**
     * Get available payment statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    /**
     * Get the booking that owns the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Generate unique payment number.
     */
    public static function generatePaymentNumber(): string
    {
        do {
            $number = 'PAY' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('payment_number', $number)->exists());

        return $number;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by payment method.
     */
    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is PayPal
     */
    public function isPayPal(): bool
    {
        return $this->payment_method === self::METHOD_PAYPAL;
    }

    /**
     * Check if payment is Stripe
     */
    public function isStripe(): bool
    {
        return $this->payment_method === self::METHOD_STRIPE;
    }

    /**
     * Check if payment can be refunded online
     */
    public function canBeRefundedOnline(): bool
    {
        return $this->isCompleted() && ($this->isPayPal() || $this->isStripe()) && $this->transaction_id;
    }

    /**
     * Get formatted payment method name
     */
    public function getMethodNameAttribute(): string
    {
        return self::getPaymentMethods()[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    /**
     * Get formatted status name
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst($this->status);
    }
}
