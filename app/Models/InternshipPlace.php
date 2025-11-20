<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipPlace extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'internship_places';

    // Primary key tabel
    protected $primaryKey = 'code';

    // Primary key bukan auto-increment (karena menggunakan string)
    public $incrementing = false;

    // Tipe data primary key
    protected $keyType = 'string';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'code',
        'name',
        'field',
        'address',
        'contact_number',
        'latitude',
        'longitude'
    ];

    /**
     * Relasi dengan model Student (jika ada)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'internship_place_code', 'code');
    }

    public function batchDetails()
    {
        return $this->hasMany(InternshipBatchDetail::class, 'place_code', 'code');
    }
}
