<?php

// App\Models\Grade.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'content',
        'audio_visual',
        'creativity_innovation',
        'social_media_upload',
        'adherence_to_guidelines',
        'video_appearance',
        'grade',
        'student_id',
        'report_id',
    ];

    // Relasi dengan Report
    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
