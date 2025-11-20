<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();  // Kolom primary key untuk students
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  // Relasi ke tabel users
            $table->string('name');
            $table->string('nis');
            $table->enum('gender', ['L', 'P']);
            $table->string('whatsapp_number');
            $table->string('telegram_number')->nullable();
            $table->string('class_code');  // Kode kelas sebagai relasi
            $table->string('department_code');  // Kode departemen sebagai relasi
            $table->string('internship_place_code');  // Kode tempat magang sebagai relasi
            $table->unsignedBigInteger('internship_batch_id');
            $table->unsignedBigInteger('mentor_id');
            $table->timestamps();

            // Menambahkan foreign key untuk class_code dan department_code
            $table->foreign('class_code')->references('code')->on('classes')->onDelete('cascade');
            $table->foreign('department_code')->references('code')->on('departments')->onDelete('cascade');
            $table->foreign('internship_place_code')->references('code')->on('internship_places')->onDelete('cascade');
            $table->foreign('internship_batch_id')->references('id')->on('internship_batches')->onDelete('cascade'); // Menambahkan foreign key ke tabel mentors
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
