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
}
