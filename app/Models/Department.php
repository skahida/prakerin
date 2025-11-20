<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'departments';

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
    ];

    /**
     * Relasi dengan model Student
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'department_code', 'code');
    }
}
