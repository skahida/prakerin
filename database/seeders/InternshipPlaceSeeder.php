<?php

namespace Database\Seeders;

use App\Models\InternshipPlace;
use App\Models\Mentor;
use Illuminate\Database\Seeder;

class InternshipPlaceSeeder extends Seeder
{
    public function run()
    {
        // Ambil data mentor pertama
        $mentor = Mentor::first();

        if (!$mentor) {
            // Buat dummy mentor jika tidak ada
            $mentor = Mentor::create([
                'name' => 'Default Mentor',
                'user_id' => 1, // Sesuaikan dengan ID user yang valid
                'gender' => 'male',
                'whatsapp_number' => '081234567890',
                'telegram_number' => 'default_mentor',
            ]);
        }

        // Tambahkan tempat magang
        InternshipPlace::create([
            'code' => 'IP001',
            'name' => 'SMK NU Al-Hidayah Kudus',
            'address' => 'Jl. Al-Hidayah, Kudus, Jawa Tengah',
            'field' => 'Teknik Komputer dan Jaringan',
            'contact_number' => '082123456789',
            'latitude' => -6.7633561,
            'longitude' => 110.8040573,
            'batch_name' => 'Batch 1',
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'mentor_id' => $mentor->id,
            'status' => 'active',
        ]);

        InternshipPlace::create([
            'code' => 'IP002',
            'name' => 'SMK Negeri 1 Kudus',
            'address' => 'Jl. Ahmad Yani, Kudus, Jawa Tengah',
            'field' => 'Akuntansi dan Keuangan',
            'contact_number' => '082987654321',
            'latitude' => -6.784423,
            'longitude' => 110.837439,
            'batch_name' => 'Batch 2',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'mentor_id' => $mentor->id,
            'status' => 'non-active',
        ]);
    }
}
