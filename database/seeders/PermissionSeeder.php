<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Rooms Permissions
            ['name' => 'View Rooms', 'slug' => 'rooms.view', 'description' => 'View room types and rooms', 'category' => 'rooms'],
            ['name' => 'Create Rooms', 'slug' => 'rooms.create', 'description' => 'Create new room types', 'category' => 'rooms'],
            ['name' => 'Edit Rooms', 'slug' => 'rooms.edit', 'description' => 'Edit existing room types', 'category' => 'rooms'],
            ['name' => 'Delete Rooms', 'slug' => 'rooms.delete', 'description' => 'Delete room types', 'category' => 'rooms'],
            
            // Bookings Permissions
            ['name' => 'View Bookings', 'slug' => 'bookings.view', 'description' => 'View all bookings', 'category' => 'bookings'],
            ['name' => 'Create Bookings', 'slug' => 'bookings.create', 'description' => 'Create new bookings', 'category' => 'bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'bookings.edit', 'description' => 'Edit existing bookings', 'category' => 'bookings'],
            ['name' => 'Delete Bookings', 'slug' => 'bookings.delete', 'description' => 'Delete bookings', 'category' => 'bookings'],
            ['name' => 'Manage Booking Status', 'slug' => 'bookings.manage-status', 'description' => 'Update booking status', 'category' => 'bookings'],
            
            // Customers Permissions
            ['name' => 'View Customers', 'slug' => 'customers.view', 'description' => 'View customer list', 'category' => 'customers'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'description' => 'Create new customers', 'category' => 'customers'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'description' => 'Edit customer information', 'category' => 'customers'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'description' => 'Delete customers', 'category' => 'customers'],
            
            // Payments Permissions
            ['name' => 'View Payments', 'slug' => 'payments.view', 'description' => 'View payment records', 'category' => 'payments'],
            ['name' => 'Create Payments', 'slug' => 'payments.create', 'description' => 'Create new payments', 'category' => 'payments'],
            ['name' => 'Edit Payments', 'slug' => 'payments.edit', 'description' => 'Edit payment records', 'category' => 'payments'],
            ['name' => 'Delete Payments', 'slug' => 'payments.delete', 'description' => 'Delete payments', 'category' => 'payments'],
            
            // Amenities Permissions
            ['name' => 'View Amenities', 'slug' => 'amenities.view', 'description' => 'View amenities list', 'category' => 'amenities'],
            ['name' => 'Create Amenities', 'slug' => 'amenities.create', 'description' => 'Create new amenities', 'category' => 'amenities'],
            ['name' => 'Edit Amenities', 'slug' => 'amenities.edit', 'description' => 'Edit amenities', 'category' => 'amenities'],
            ['name' => 'Delete Amenities', 'slug' => 'amenities.delete', 'description' => 'Delete amenities', 'category' => 'amenities'],
            
            // Admins Permissions
            ['name' => 'View Admins', 'slug' => 'admins.view', 'description' => 'View admin list', 'category' => 'admins'],
            ['name' => 'Create Admins', 'slug' => 'admins.create', 'description' => 'Create new admins', 'category' => 'admins'],
            ['name' => 'Edit Admins', 'slug' => 'admins.edit', 'description' => 'Edit admin information and permissions', 'category' => 'admins'],
            ['name' => 'Delete Admins', 'slug' => 'admins.delete', 'description' => 'Delete admins', 'category' => 'admins'],
            
            // Settings Permissions
            ['name' => 'View Settings', 'slug' => 'settings.view', 'description' => 'View system settings', 'category' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'description' => 'Edit system settings', 'category' => 'settings'],
            
            // Dashboard Permissions
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'description' => 'View admin dashboard', 'category' => 'dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
