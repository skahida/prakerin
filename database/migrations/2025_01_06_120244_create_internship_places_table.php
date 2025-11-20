<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternshipPlacesTable extends Migration
{
    public function up()
    {
        Schema::create('internship_places', function (Blueprint $table) {
            $table->string('code')->primary(); // Menggunakan kode sebagai primary key
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('field');
            $table->string('contact_number');

            // Membagi lokasi koordinat menjadi latitude dan longitude
            $table->decimal('latitude', 10, 7)->nullable();  // Kolom latitude
            $table->decimal('longitude', 10, 7)->nullable(); // Kolom longitude
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('internship_places');
    }
}
