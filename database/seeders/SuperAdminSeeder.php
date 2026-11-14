<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check whether SuperAdmin already exists
        $exists = DB::table('users')
            ->where('role', 'superadmin')
            ->exists();

        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('SuperAdmin@123'),
                'role' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('SuperAdmin created successfully.');
        } else {
            $this->command->info('SuperAdmin already exists.');
        }
    }
}
