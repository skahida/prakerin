<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipBatch extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda
    protected $table = 'internship_batches';

    // Tentukan kolom-kolom yang dapat diisi (fillable)
    protected $fillable = [
        'batch_name',
        'start_date',
        'end_date',
        'academic_year',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'internship_batch_id', 'id');
    }
}
