<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresencesTable extends Migration
{
    public function up()
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();  // ID utama tabel presences
            $table->unsignedBigInteger('student_id');  // Kolom student_id sebagai foreign key
            $table->timestamp('check_in')->nullable();  // Waktu check-in, nullable
            $table->timestamp('check_out')->nullable();  // Waktu check-out, nullable

            // Kolom latitude dan longitude untuk check-in
            $table->decimal('check_in_latitude', 10, 7)->nullable();  // Latitude lokasi check-in
            $table->decimal('check_in_longitude', 10, 7)->nullable();  // Longitude lokasi check-in
            $table->string('check_in_location_link')->nullable();  // Link lokasi check-in

            // Kolom latitude dan longitude untuk check-out
            $table->decimal('check_out_latitude', 10, 7)->nullable();  // Latitude lokasi check-out
            $table->decimal('check_out_longitude', 10, 7)->nullable();  // Longitude lokasi check-out
            $table->string('check_out_location_link')->nullable();  // Link lokasi check-out

            $table->timestamps();

            // Menambahkan foreign key untuk student_id
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('presences');
    }
}
