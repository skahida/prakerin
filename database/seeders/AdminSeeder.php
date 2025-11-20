<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Mengambil user dengan ID 1-5 yang memiliki role 'admin'
        $users = User::whereBetween('id', [1, 5])->get(); // Mengambil user dengan ID 1 sampai 5

        // Menambahkan data admin untuk masing-masing user
        foreach ($users as $user) {
            Admin::create([
                'user_id' => $user->id,  // Menghubungkan dengan user yang sudah ada
                'name' => "Admin " . $user->username,  // Nama Admin berdasarkan username user
            ]);
        }
    }
}
