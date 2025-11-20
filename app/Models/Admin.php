<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'admins';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'name',
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
}
