<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegrams', function (Blueprint $table) {
            $table->id(); // ID kolom otomatis ditambahkan oleh Laravel
            $table->string('bot_token', 255); // Kolom untuk menyimpan bot token
            $table->text('message'); // Kolom untuk menyimpan pesan
            $table->string('username', 225); // Kolom untuk menyimpan pesan
            $table->timestamps(); // Menyimpan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegrams');
    }
}
