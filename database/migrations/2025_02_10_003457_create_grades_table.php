<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade'); // Relasi dengan siswa
            $table->foreignId('report_id')->constrained()->onDelete('cascade'); // Relasi dengan report
            $table->text('content')->nullable(); // Content column (isi konten)
            $table->text('audio_visual')->nullable(); // Audio Visual column
            $table->text('creativity_innovation')->nullable(); // Creativity and Innovation column
            $table->text('social_media_upload')->nullable(); // Social Media Upload (IG, TikTok, FB)
            $table->text('adherence_to_guidelines')->nullable(); // Adherence to guidelines
            $table->decimal('grade', 5, 2)->nullable(); // Grade column (nilai)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
};
