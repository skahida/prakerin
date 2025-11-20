<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'reports';

    // Mass assignable attributes
    protected $fillable = [
        'student_id',
        'report_title',
        'report_link1',
        'report_link2',
        'report_link3',
        'report_date',
        // 'description',
        // 'status'
    ];

    // Relasi dengan Grade
    public function grades()
    {
        return $this->hasMany(Grade::class, 'report_id');
    }

    // Defining relationship with the Student model
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
