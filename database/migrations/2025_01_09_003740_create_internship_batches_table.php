<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternshipBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('internship_batches', function (Blueprint $table) {
            $table->id(); // Kolom ID sebagai primary key
            $table->string('batch_name'); // Nama gelombang
            $table->date('start_date');   // Tanggal mulai gelombang
            $table->date('end_date');     // Tanggal akhir gelombang
            $table->string('academic_year')->nullable(); // Tahun pelajaran
            $table->enum('status_batch', ['active', 'non-active'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('internship_batches');
    }
}
