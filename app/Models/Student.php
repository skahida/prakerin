<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'nis',
        'gender',
        'whatsapp_number',
        'telegram_number',
        'class_code',
        'department_code',
        'internship_place_code',
        'internship_batch_id',
        'mentor_id',
    ];

    /**
     * Relationships
     */

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke tabel classes
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_code', 'code');
    }

    // Relasi ke tabel departments
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    // Relasi ke tabel internship_places
    public function internshipPlace()
    {
        return $this->belongsTo(InternshipPlace::class, 'internship_place_code', 'code');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id', 'id'); // 'mentor_id' adalah kolom di students yang mengarah ke mentor
    }

    // Relasi ke tabel internship_places
    public function internshipBatch()
    {
        return $this->belongsTo(InternshipBatch::class, 'internship_batch_id', 'id');
    }

    public function presence()
    {
        return $this->hasMany(Presence::class, 'student_id', 'id'); // Menghubungkan mentor dengan banyak siswa
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'student_id'); // Pastikan 'student_id' sesuai dengan kolom yang ada di tabel reports
    }

    // Di model Student
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id', 'id'); // Sesuaikan dengan relasi yang sesuai
    }
}
