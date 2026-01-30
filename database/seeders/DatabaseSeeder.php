<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AmenitySeeder::class,
            RoomTypeSeeder::class,
        ]);

        // Create default settings
        Setting::setValue('hotel_name', 'Grand Hotel', 'string', 'Hotel name');
        Setting::setValue('currency', 'USD', 'string', 'Currency code');
        Setting::setValue('currency_symbol', '$', 'string', 'Currency symbol');
        Setting::setValue('tax_rate', '10', 'decimal', 'Tax rate percentage');
        Setting::setValue('check_in_time', '14:00', 'string', 'Default check-in time');
        Setting::setValue('check_out_time', '12:00', 'string', 'Default check-out time');
        Setting::setValue('contact_email', 'info@grandhotel.com', 'string', 'Contact email');
        Setting::setValue('contact_phone', '+1 234 567 8900', 'string', 'Contact phone');
        Setting::setValue('contact_address', '123 Grand Avenue, City, Country', 'string', 'Contact address');
    }
}
