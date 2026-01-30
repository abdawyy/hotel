<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Amenity;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = Amenity::all();

        // Standard Room
        $standard = RoomType::create([
            'name' => 'Standard Room',
            'description' => 'Comfortable standard room with essential amenities. Perfect for solo travelers or couples.',
            'price_per_night' => 99.00,
            'max_guests' => 2,
            'max_adults' => 2,
            'max_children' => 1,
            'total_rooms' => 10,
            'is_active' => true,
        ]);
        $standard->amenities()->attach($amenities->whereIn('name', ['Free WiFi', 'Air Conditioning', 'TV', 'Safe'])->pluck('id'));

        // Create rooms for standard type
        for ($i = 1; $i <= 10; $i++) {
            Room::create([
                'room_type_id' => $standard->id,
                'room_number' => 'STD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }

        // Deluxe Room
        $deluxe = RoomType::create([
            'name' => 'Deluxe Room',
            'description' => 'Spacious deluxe room with premium amenities and city view. Ideal for families or extended stays.',
            'price_per_night' => 149.00,
            'max_guests' => 4,
            'max_adults' => 3,
            'max_children' => 2,
            'total_rooms' => 8,
            'is_active' => true,
        ]);
        $deluxe->amenities()->attach($amenities->whereIn('name', ['Free WiFi', 'Air Conditioning', 'TV', 'Mini Bar', 'Safe', 'Coffee Maker'])->pluck('id'));

        // Create rooms for deluxe type
        for ($i = 1; $i <= 8; $i++) {
            Room::create([
                'room_type_id' => $deluxe->id,
                'room_number' => 'DLX-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }

        // Suite
        $suite = RoomType::create([
            'name' => 'Executive Suite',
            'description' => 'Luxurious executive suite with separate living area, premium amenities, and stunning views.',
            'price_per_night' => 249.00,
            'max_guests' => 4,
            'max_adults' => 4,
            'max_children' => 2,
            'total_rooms' => 5,
            'is_active' => true,
        ]);
        $suite->amenities()->attach($amenities->pluck('id')); // All amenities

        // Create rooms for suite type
        for ($i = 1; $i <= 5; $i++) {
            Room::create([
                'room_type_id' => $suite->id,
                'room_number' => 'SUITE-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
    }
}
