<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'room_type_id',
        'room_id',
        'quantity',
        'price_per_night',
        'nights',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_per_night' => 'decimal:2',
        'nights' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the detail.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the room type for this detail.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the assigned room for this detail.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
