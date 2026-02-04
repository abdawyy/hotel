<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_night',
        'max_guests',
        'max_adults',
        'max_children',
        'total_rooms',
        'is_active',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'max_guests' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'total_rooms' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all rooms of this type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get all images for this room type.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class);
    }

    /**
     * Get the primary/featured image for this room type.
     */
    public function primaryImage()
    {
        return $this->hasOne(RoomImage::class)->where('is_primary', true);
    }

    /**
     * Get the display image (primary image or first image as fallback).
     */
    public function getDisplayImageAttribute()
    {
        return $this->primaryImage ?? $this->images()->orderBy('display_order')->orderBy('id')->first();
    }

    /**
     * Get all amenities for this room type.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity');
    }

    /**
     * Get all booking details for this room type.
     */
    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    /**
     * Scope to get only active room types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter room types that have availability between given dates.
     * Only returns room types where at least one room is available for ALL nights in the range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon|string  $checkIn
     * @param  \Carbon\Carbon|string  $checkOut
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailableBetween($query, $checkIn, $checkOut)
    {
        $checkIn = \Carbon\Carbon::parse($checkIn);
        $checkOut = \Carbon\Carbon::parse($checkOut);

        return $query->where(function ($q) use ($checkIn, $checkOut) {
            $q->whereRaw('total_rooms > (
                SELECT COALESCE(SUM(bd.quantity), 0)
                FROM booking_details bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                WHERE bd.room_type_id = room_types.id
                AND b.check_in_date < ?
                AND b.check_out_date > ?
                AND b.status NOT IN (?, ?)
            )', [$checkOut, $checkIn, 'cancelled', 'checked_out']);
        });
    }

    /**
     * Get available rooms count for a date range.
     * Uses total_rooms as the cap: only that many can be reserved at the same time.
     * If all total_rooms are already booked for overlapping dates, returns 0.
     */
    public function getAvailableRoomsCount($checkIn, $checkOut): int
    {
        $totalRooms = (int) $this->total_rooms;

        // Count how many rooms of this type are already booked for overlapping dates
        // Overlap: booking.check_in < requested.check_out AND booking.check_out > requested.check_in
        $bookedCount = (int) BookingDetail::where('room_type_id', $this->id)
            ->whereHas('booking', function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn)
                    ->whereNotIn('status', ['cancelled', 'checked_out']);
            })
            ->sum('quantity');

        // Available = total_rooms minus already booked; if full, return 0
        return max(0, $totalRooms - $bookedCount);
    }

    /**
     * Get only the specific dates where reservations exceed the limit (fully booked that night).
     * Used to disable only those dates in the calendar; all other dates stay selectable.
     *
     * @param  \Carbon\Carbon|string  $from  Start date (inclusive)
     * @param  \Carbon\Carbon|string  $to    End date (inclusive)
     * @return array Array of date strings (Y-m-d) - only nights with zero availability
     */
    public function getUnavailableDates($from, $to): array
    {
        $from = \Carbon\Carbon::parse($from)->startOfDay();
        $to = \Carbon\Carbon::parse($to)->endOfDay();
        $totalRooms = (int) $this->total_rooms;
        if ($totalRooms < 1) {
            return [];
        }

        $details = BookingDetail::where('room_type_id', $this->id)
            ->whereHas('booking', function ($query) use ($from, $to) {
                $query->where('check_in_date', '<', $to)
                    ->where('check_out_date', '>', $from)
                    ->whereNotIn('status', ['cancelled', 'checked_out']);
            })
            ->with('booking:id,check_in_date,check_out_date')
            ->get(['id', 'booking_id', 'quantity']);

        $unavailable = [];
        $current = $from->copy();
        while ($current <= $to) {
            $date = $current->format('Y-m-d');
            $booked = 0;
            foreach ($details as $d) {
                $checkIn = \Carbon\Carbon::parse($d->booking->check_in_date);
                $checkOut = \Carbon\Carbon::parse($d->booking->check_out_date);
                if ($checkIn->format('Y-m-d') <= $date && $checkOut->format('Y-m-d') > $date) {
                    $booked += (int) $d->quantity;
                }
            }
            // Only add this date when it exceeds the limit (no rooms left for that night)
            if ($booked >= $totalRooms) {
                $unavailable[] = $date;
            }
            $current->addDay();
        }

        return $unavailable;
    }
}
