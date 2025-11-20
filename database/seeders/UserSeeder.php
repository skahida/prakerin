<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            $role = 'student'; // Default role

            // Tentukan role berdasarkan index
            if ($i <= 5) {
                $role = 'admin'; // User 1-5 adalah admin
            } elseif ($i >= 16) {
                $role = 'mentor'; // User 16-20 adalah mentor
            }

            // Periksa apakah username sudah ada
            if (!User::where('username', "user$i")->exists()) {
                User::create([
                    'name' => "User $i", // Kolom name
                    'username' => "user$i", // Kolom username
                    'password' => Hash::make('password'), // Hash password
                    'role' => $role, // Tambahkan role
                ]);
            }
        }
    }
}
