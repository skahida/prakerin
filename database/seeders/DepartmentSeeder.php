<?php

namespace Database\Seeders;

use App\Models\Department;  // Pastikan untuk mengimpor model Department
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan beberapa data department untuk seeding
        $departments = [
            [
                'code' => 'D001',
                'name' => 'Teknik Informatika',
            ],
            [
                'code' => 'D002',
                'name' => 'Sistem Informasi',
            ],
            [
                'code' => 'D003',
                'name' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'code' => 'D004',
                'name' => 'Manajemen',
            ],
            [
                'code' => 'D005',
                'name' => 'Akuntansi',
            ],
            // Anda bisa menambahkan lebih banyak department di sini
        ];

        // Looping untuk memasukkan data department
        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
