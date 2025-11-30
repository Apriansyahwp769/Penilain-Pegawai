<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // Admin global
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),

                'nip' => '19876543210001',
                'division_id' => null,  // admin tidak pakai divisi
                'position_id' => null,  // admin tidak pakai posisi
                'phone' => '081234567890',
                'join_date' => '2023-01-01',

                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ketua Divisi IT
            [
                'name' => 'Ketua Divisi IT',
                'email' => 'ketua.it@example.com',
                'password' => Hash::make('password123'),

                'nip' => '19876543210002',
                'division_id' => 1,  // IT
                'position_id' => 2,  // Supervisor
                'phone' => '081298765432',
                'join_date' => '2023-05-01',

                'role' => 'ketua_divisi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Staff IT
            [
                'name' => 'Staff IT',
                'email' => 'staff.it@example.com',
                'password' => Hash::make('password123'),

                'nip' => '19876543210003',
                'division_id' => 1,  // IT
                'position_id' => 3,  // Staff
                'phone' => '089612345678',
                'join_date' => '2024-01-01',

                'role' => 'staff',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
