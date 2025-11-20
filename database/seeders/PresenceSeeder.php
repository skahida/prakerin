<?php

namespace Database\Seeders;

use App\Models\Presence;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PresenceSeeder extends Seeder
{
    public function run()
    {
        // Mengambil semua mahasiswa yang ada di sistem
        $students = Student::all();

        // Menambahkan data presensi untuk masing-masing mahasiswa
        foreach ($students as $student) {
            // Koordinat SMK NU Al-Hidayah Kudus (untuk check-in dan check-out)
            $latitude = -6.7633561;
            $longitude = 110.8040573;

            // Koordinat acak untuk check-out (misalnya sekitar lokasi SMK NU Al-Hidayah Kudus)
            $latitudeCheckOut = $latitude + rand(-1, 1) * 0.001; // Menggeser sedikit koordinat untuk check-out
            $longitudeCheckOut = $longitude + rand(-1, 1) * 0.001;

            // Link Google Maps dinamis untuk check-in dan check-out
            $checkInLink = "https://www.google.com/maps?q=$latitude,$longitude";
            $checkOutLink = "https://www.google.com/maps?q=$latitudeCheckOut,$longitudeCheckOut";

            // Menambahkan data presensi
            Presence::create([
                'student_id' => $student->id,  // Menghubungkan dengan mahasiswa
                'check_in' => Carbon::now()->subDays(rand(1, 30)),  // Waktu check-in acak (1 hingga 30 hari yang lalu)
                'check_out' => Carbon::now()->subDays(rand(1, 30))->addHours(rand(1, 5)),  // Waktu check-out acak
                'check_in_latitude' => $latitude,  // Latitude check-in
                'check_in_longitude' => $longitude,  // Longitude check-in
                'check_in_location_link' => $checkInLink,  // Link check-in
                'check_out_latitude' => $latitudeCheckOut,  // Latitude check-out
                'check_out_longitude' => $longitudeCheckOut,  // Longitude check-out
                'check_out_location_link' => $checkOutLink,  // Link check-out
            ]);
        }
    }
}
