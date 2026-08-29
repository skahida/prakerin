<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->json('activities')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['draft', 'submitted', 'signed'])->default('draft');

            // DUDI Supervisor
            $table->string('dudi_supervisor_name')->nullable();
            $table->string('dudi_supervisor_signature')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};
