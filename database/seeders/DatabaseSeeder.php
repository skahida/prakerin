<?php

namespace Database\Seeders;

use App\Models\InternshipPlace;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Menjalankan seeder
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            // MentorSeeder::class,
            // ClassSeeder::class,
            // DepartmentSeeder::class,
            // InternshipPlaceSeeder::class,
            // StudentSeeder::class,
            // PresenceSeeder::class
        ]);
    }
}
