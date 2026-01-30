<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access',
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'description' => 'Regular customer user',
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'phone' => '+1 234 567 8900',
            'email_verified_at' => now(),
        ]);

        // Create sample customer users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
            'phone' => '+1 234 567 8901',
            'address' => '123 Main St, City, Country',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
            'phone' => '+1 234 567 8902',
            'address' => '456 Oak Ave, City, Country',
            'email_verified_at' => now(),
        ]);
    }
}
