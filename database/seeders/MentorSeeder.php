<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    public function run()
    {
        $users = User::whereBetween('id', [16, 20])->get();

        if ($users->isEmpty()) {
            // Buat dummy data untuk users jika kosong
            for ($i = 16; $i <= 20; $i++) {
                $users[] = User::create([
                    'id' => $i,
                    'username' => 'user' . $i,
                    'password' => Hash::make('password'), // Hash password
                    'role' => 'mentor',
                ]);
            }
        }

        foreach ($users as $user) {
            Mentor::create([
                'user_id' => $user->id,
                'name' => "Mentor " . $user->username,
                'gender' => $user->id % 2 == 0 ? 'male' : 'female',
                'whatsapp_number' => '08' . rand(100000000, 999999999),
                'telegram_number' => 'mentor' . $user->username,
            ]);
        }
    }
}
