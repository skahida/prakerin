<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->unsignedBigInteger('student_id');  // Foreign key from students table
            $table->string('report_title');
            $table->string('report_link1')->nullable();  // Link to the first report video
            $table->string('report_link2')->nullable();  // Link to the second report video
            $table->string('report_link3')->nullable();  // Link to the third report video
            $table->text('description')->nullable();  // Description of the report
            $table->date('report_date');
            $table->enum('report_status', ['Belum Upload', 'Sudah Upload', 'Sudah Diperiksa'])->default('Belum Upload');  // Status of the report
            $table->timestamps();  // Timestamps for created_at and updated_at

            // Foreign key constraint linking to the students table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
