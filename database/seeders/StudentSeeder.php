<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Mengambil user dengan ID 6 sampai 15 yang akan menjadi mahasiswa (student)
        $users = User::whereBetween('id', [6, 15])->get(); // Mengambil user dengan ID 6 sampai 15

        // Menambahkan data mahasiswa untuk masing-masing user
        foreach ($users as $user) {
            Student::create([
                'user_id' => $user->id,  // Menghubungkan dengan user yang sudah ada
                'name' => "Student " . $user->username,  // Nama mahasiswa berdasarkan username user
                'gender' => $user->id % 2 == 0 ? 'male' : 'female',  // Gender bergantian antara male dan female
                'whatsapp_number' => '08' . rand(100000000, 999999999),  // Generate nomor WhatsApp acak
                'telegram_number' => 'student' . $user->username,  // Telegram dengan username
                'class_code' => 'A1',  // Misalnya class_code A1
                'department_code' => 'D001',  // Misalnya department_code D001
                'internship_place_code' => 'IP001',  // Misalnya internship_place_code IP001
            ]);
        }
    }
}
