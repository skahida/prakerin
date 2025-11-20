<?php

namespace Database\Seeders;

use App\Models\ClassModel;  // Mengimpor model ClassModel
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan beberapa data kelas untuk seeding
        $classes = [
            [
                'code' => 'A1',
                'name' => 'Kelas A - Teknik Informatika',
            ],
            [
                'code' => 'A2',
                'name' => 'Kelas A - Sistem Informasi',
            ],
            [
                'code' => 'B1',
                'name' => 'Kelas B - Rekayasa Perangkat Lunak',
            ],
            [
                'code' => 'B2',
                'name' => 'Kelas B - Manajemen',
            ],
            [
                'code' => 'C1',
                'name' => 'Kelas C - Akuntansi',
            ],
            // Anda bisa menambahkan lebih banyak kelas sesuai kebutuhan
        ];

        // Looping untuk memasukkan data kelas
        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}
