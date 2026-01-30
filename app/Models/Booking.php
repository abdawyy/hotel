<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_number',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'status',
        'total_price',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'special_requests',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all booking details for this booking.
     */
    public function details(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    /**
     * Get all payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Generate unique booking number.
     */
    public static function generateBookingNumber(): string
    {
        do {
            $number = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    /**
     * Calculate number of nights.
     */
    public function getNightsAttribute(): int
    {
        return Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date));
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get today's check-ins.
     */
    public function scopeTodayCheckIns($query)
    {
        return $query->where('check_in_date', Carbon::today())
                     ->whereIn('status', ['confirmed', 'checked_in']);
    }

    /**
     * Scope to get today's check-outs.
     */
    public function scopeTodayCheckOuts($query)
    {
        return $query->where('check_out_date', Carbon::today())
                     ->whereIn('status', ['checked_in', 'checked_out']);
    }
}
