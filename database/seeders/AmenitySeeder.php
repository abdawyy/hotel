<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Free WiFi', 'icon' => 'bi bi-wifi', 'description' => 'High-speed internet access'],
            ['name' => 'Air Conditioning', 'icon' => 'bi bi-thermometer-half', 'description' => 'Climate control'],
            ['name' => 'TV', 'icon' => 'bi bi-tv', 'description' => 'Flat-screen TV with cable channels'],
            ['name' => 'Mini Bar', 'icon' => 'bi bi-cup-straw', 'description' => 'Stocked mini refrigerator'],
            ['name' => 'Room Service', 'icon' => 'bi bi-bell', 'description' => '24/7 room service available'],
            ['name' => 'Safe', 'icon' => 'bi bi-shield-lock', 'description' => 'In-room safe'],
            ['name' => 'Balcony', 'icon' => 'bi bi-door-open', 'description' => 'Private balcony with view'],
            ['name' => 'Bathtub', 'icon' => 'bi bi-droplet', 'description' => 'Luxurious bathtub'],
            ['name' => 'Coffee Maker', 'icon' => 'bi bi-cup', 'description' => 'Coffee and tea making facilities'],
            ['name' => 'Hair Dryer', 'icon' => 'bi bi-wind', 'description' => 'Hair dryer available'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}
