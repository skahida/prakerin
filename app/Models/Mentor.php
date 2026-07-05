<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'mentors';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'whatsapp_number',
        'telegram_number',
    ];

    /**
     * Relasi ke model User
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class, 'mentor_id', 'id'); // Menghubungkan mentor dengan banyak siswa
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'mentor_id', 'id');
    }

}
