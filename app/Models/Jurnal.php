<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'activities',
        'description',
        'photo',
        'status',
        'dudi_supervisor_name',
        'dudi_supervisor_signature',
        'signed_at',
    ];

    protected $casts = [
        'activities' => 'array',
        'date'       => 'date',
        'signed_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
