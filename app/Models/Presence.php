<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'presences';

    // Tentukan kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'student_id',
        'check_in',
        'check_out',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_location_link',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_location_link',
        'status',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
